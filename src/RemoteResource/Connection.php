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

use Guzzle\Http\Client;

class Connection {
  // Create a guzzle client
  public static function client() {
    $client = new Client();
    $client->setDefaultOption('exceptions', false);
    return $client;
  }

  // GET
  public static function get($path) {
    return self::handleResponse(self::client()->get($path, self::headers())->send());
  }

  // POST
  public static function post($path, $attributes = array()) {
    return self::handleResponse(self::client()->post($path, self::headers(), json_encode($attributes))->send());
  }

  // PATCH
  public static function patch($path, $attributes = array()) {
    return self::handleResponse(self::client()->patch($path, self::headers(), json_encode($attributes))->send());
  }

  // DELETE
  public static function delete($path) {
    return self::handleResponse(self::client()->delete($path, self::headers())->send());
  }

  public static function headers() {
    $credentials = Config::base64EncodedCredentials();

    return array(
      'Content-Type' => 'application/json',
      'Authorization' => 'Basic '.$credentials
    );
  }

  private static function handleResponse($response) {
    $decoded_body = json_decode( $response->getBody(), true );

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
