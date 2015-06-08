<?php
namespace RemoteResource;

use RemoteResource\Config;
use RemoteResource\Exception;
use RemoteResource\Exception\BadRequest;
use RemoteResource\Exception\UnauthorizedAccess;
use RemoteResource\Exception\ForbiddenAccess;
use RemoteResource\Exception\ResourceNotFound;
use RemoteResource\Exception\MethodNotAllowed;
use RemoteResource\Exception\ResourceConflict;
use RemoteResource\Exception\ResourceGone;
use RemoteResource\Exception\ResourceInvalid;
use RemoteResource\Exception\ClientError;
use RemoteResource\Exception\ServerError;
use RemoteResource\Exception\ConnectionError;
use RemoteResource\Exception\RequestTimeout;
use RemoteResource\Connection\Request;

use Guzzle\Http\Client;

/**
 * The connection class sits on top of the (usually) Guzzle HTTP client and
 * provides us an easy to use wrapper.
 */
class Connection {
  public $headers;
  private $client, $config, $formatter;

  /**
   * @param Config $config the configuration object for this RemoteResource
   */
  public function __construct(Config $config) {
    $this->config = $config;
    $this->formatter = $config->formatter();
    $this->headers = $config->headers()->toArray();
  }

  /**
   * Send a GET request to $path
   * @param  string $path url for the request
   * @return array        decoded response body
   */
  public function get($path) {
    return $this->sendRequest( 'GET', $path );
  }

  /**
   * Send a POST request to $path, with $attributes in the body
   * @param  string $path       url for the request
   * @param  array  $attributes attributes to pass in the request body
   * @return array              decoded response body
   */
  public function post($path, $attributes = array()) {
    return $this->sendRequest( 'POST', $path, $attributes );
  }

  /**
   * Send a PATCH request to $path, with $attributes in the body
   * @param  string $path       url for the request
   * @param  array  $attributes attributes for the resource
   * @return array              decoded response body
   */
  public function patch($path, $attributes = array()) {
    return $this->sendRequest( 'PATCH', $path, $attributes );
  }

  /**
   * Send a DELETE request to $path
   * @param  string $path url for the request
   * @return array        decoded response body
   */
  public function delete($path) {
    return $this->sendRequest( 'DELETE', $path );
  }

  /**
   * Init client if it's not already, return client
   * @return mixed HTTP Client
   */
  public function client() {
    if (!$this->client) {
      $client = new Client;
      $client->setDefaultOption('exceptions', false);
      $this->client = $client;
    }

    return $this->client;
  }

  /**
   * Mostly for mocking purposes
   * @param mixed $client the HTTP client to use, default is Guzzle (it would have to conform to guzzle's API)
   */
  public function setClient($client) {
    $this->client = $client;
  }

  /**
   * @todo Add this to a helper class or utility class somewhere, wrap the timer call in sendRequest in a "debug" config option
   * @param  [type] $ru    [description]
   * @param  [type] $rus   [description]
   * @param  [type] $index [description]
   * @return [type]        [description]
   */
  private function rutime($ru, $rus, $index) {
    return ($ru["ru_$index.tv_sec"]*1000 + intval($ru["ru_$index.tv_usec"]/1000))
     - ($rus["ru_$index.tv_sec"]*1000 + intval($rus["ru_$index.tv_usec"]/1000));
  }

  // ----------------------------
  // _____ PRIVATE METHODS ______
  // ____________________________

  /**
   * Sends the request via guzzle and returns the needed parts of the response, or throws an
   * exception.
   * @param  string $verb HTTP verb: GET, POST, PUT, PATCH, DELETE, etc.
   * @param  string $path resource url
   * @param  array  $body array of properties to be converted to JSON/XML/etc
   * @return array        decoded body
   * @throws RemoteResource\Exception corresponds to HTTP status returned
   */
  private function sendRequest($verb, $path, $body = null) {
    $rustart = getrusage();

    try {
      $request = $this->client()->createRequest($verb, $path, $this->headers, $body);
      $response = $request->send();
    } catch (\Guzzle\Http\Exception\RequestException $e) {
      throw new Exception\ConnectionError("Guzzle exception: ", $e->getMessage());
    }

    $decoded_body = $this->formatter->formatResponse( $response->getBody() );

    $ru = getrusage();
    RemoteResource::logger()->debug("Request to {$verb} {$path} took ".$this->rutime($ru, $rustart, "utime")."ms in the network\n");

    if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 400) {
      return $decoded_body;

    } elseif ($response->getStatusCode() == 400) {
      throw new Exception\BadRequest($decoded_body);

    } elseif ($response->getStatusCode() == 401) {
      throw new Exception\UnauthorizedAccess($decoded_body);

    } elseif ($response->getStatusCode() == 403) {
      throw new Exception\ForbiddenAccess($decoded_body);

    } elseif ($response->getStatusCode() == 404) {
      throw new Exception\ResourceNotFound($decoded_body);

    } elseif ($response->getStatusCode() == 405) {
      throw new Exception\MethodNotAllowed($decoded_body);

    } elseif ($response->getStatusCode() == 408) {
      throw new Exception\RequestTimeout($decoded_body);

    } elseif ($response->getStatusCode() == 409) {
      throw new Exception\ResourceConflict($decoded_body);

    } elseif ($response->getStatusCode() == 410) {
      throw new Exception\ResourceGone($decoded_body);

    } elseif ($response->getStatusCode() == 422) {
      throw new Exception\ResourceInvalid($decoded_body);

    } elseif ($response->getStatusCode() >= 401 && $response->getStatusCode() < 500) {
      throw new Exception\ClientError($decoded_body);

    } elseif ($response->getStatusCode() >= 500 && $response->getStatusCode() < 600) {
      throw new Exception\ServerError($decoded_body);

    } else {
      throw new Exception\ConnectionError($decoded_body, "Unknown response code");
    }
  }
}
