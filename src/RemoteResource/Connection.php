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

class Connection {
  public $headers;
  private $client, $config, $formatter;

  public function __construct(Config $config) {
    $this->config = $config;
    $this->formatter = $config->formatter();
    $this->headers = $config->headers();
  }

  public function get($path) {
    return $this->sendRequest( 'GET', $path );
  }

  public function post($path, $attributes = array()) {
    return $this->sendRequest( 'POST', $path, $attributes );
  }

  public function patch($path, $attributes = array()) {
    return $this->sendRequest( 'PATCH', $path, $attributes );
  }

  public function delete($path) {
    return $this->sendRequest( 'DELETE', $path );
  }

  public function client() {
    if (!$this->client) {
      $client = new Client;
      $client->setDefaultOption('exceptions', false);
      $this->client = $client;
    }

    return $this->client;
  }

  public function setClient($client) {
    $this->client = $client;
  }

  // ----------------------------
  // _____ PRIVATE METHODS ______
  // ____________________________

  private function sendRequest($verb, $path, $body = null) {
    if ($body) $body = $this->formatter->formatRequest($body);

    $request = $this->client()->createRequest($verb, $path, $this->headers, $body);
    $response = $this->handleResponse( $request->send() );
    return $response;
  }

  private function handleResponse($response) {
    $decoded_body = $this->formatter->formatResponse( $response->getBody() );

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
