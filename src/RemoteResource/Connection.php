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

class Connection {
  // GET
  public static function get($path) {
    return self::handleResponse(\Requests::get($path, self::headers()));
  }

  // POST
  public static function post($path, $attributes = array()) {
    return self::handleResponse(\Requests::post($path, self::headers(), json_encode($attributes)));
  }

  // PATCH
  public static function patch($path, $attributes = array()) {
    return self::handleResponse(\Requests::patch($path, self::headers(), json_encode($attributes)));
  }

  // DELETE
  public static function delete($path) {
    return self::handleResponse(\Requests::delete($path, self::headers()));
  }

  public static function headers() {
    $credentials = Config::base64EncodedCredentials();

    return array(
      'Content-Type' => 'application/json',
      'Authorization' => 'Basic '.$credentials
    );
  }

  private static function handleResponse($response) {
    $decoded_body = json_decode( $response->body, true );

    if ($response->status_code >= 200 && $response->status_code < 400) {
      return $decoded_body;

    } elseif ($response->status_code == 400) {
      throw new Exception\BadRequest($decoded_body);

    } elseif ($response->status_code == 401) {
      throw new Exception\UnauthorizedAccess($decoded_body);

    } elseif ($response->status_code == 403) {
      throw new Exception\ForbiddenAccess($decoded_body);

    } elseif ($response->status_code == 404) {
      throw new Exception\ResourceNotFound($decoded_body);

    } elseif ($response->status_code == 405) {
      throw new Exception\MethodNotAllowed($decoded_body);

    } elseif ($response->status_code == 409) {
      throw new Exception\ResourceConflict($decoded_body);

    } elseif ($response->status_code == 410) {
      throw new Exception\ResourceGone($decoded_body);

    } elseif ($response->status_code == 422) {
      throw new Exception\ResourceInvalid($decoded_body);

    } elseif ($response->status_code >= 401 && $response->status_code < 500) {
      throw new Exception\ClientError($decoded_body);

    } elseif ($response->status_code >= 500 && $response->status_code < 600) {
      throw new Exception\ServerError($decoded_body);

    } else {
      throw new Exception\ConnectionError($decoded_body, "Unknown response code");
    }
  }
}
