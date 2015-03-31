<?php
require_once 'Requests/Requests.php';
require_once 'lib/RemoteResourceException.php';
Requests::register_autoloader();

class BasicRemoteResource {

  // GET
  public static function get($path) {
    return self::handleResponse(Requests::get($path, self::headers()));
  }

  // POST
  public static function post($path, $attributes = array()) {
    return self::handleResponse(Requests::post($path, self::headers(), json_encode($attributes)));
  }

  // PATCH
  public static function patch($path, $attributes = array()) {
    return self::handleResponse(Requests::patch($path, self::headers(), json_encode($attributes)));
  }

  // DELETE
  public static function delete($path) {
    return self::handleResponse(Requests::delete($path, self::headers()));
  }

  public static function headers() { // probably make protected or private
    $encoded = base64_encode('user:password'); # TODO: refactor
    return array(
      'Content-Type' => 'application/json',
      'Authorization' => 'Basic '.$encoded
    );
  }

  private static function handleResponse($response) {
    $decoded_body = json_decode( $response->body, true );

    if ($response->status_code >= 200 && $response->status_code < 400) {
      return $decoded_body;

    } elseif ($response->status_code == 400) {
      throw new RemoteResourceBadRequest($decoded_body);

    } elseif ($response->status_code == 401) {
      throw new RemoteResourceUnauthorizedAccess($decoded_body);

    } elseif ($response->status_code == 403) {
      throw new RemoteResourceForbiddenAccess($decoded_body);

    } elseif ($response->status_code == 404) {
      throw new RemoteResourceResourceNotFound($decoded_body);

    } elseif ($response->status_code == 405) {
      throw new RemoteResourceMethodNotAllowed($decoded_body);

    } elseif ($response->status_code == 409) {
      throw new RemoteResourceResourceConflict($decoded_body);

    } elseif ($response->status_code == 410) {
      throw new RemoteResourceResourceGone($decoded_body);

    } elseif ($response->status_code == 422) {
      throw new RemoteResourceResourceInvalid($decoded_body);

    } elseif ($response->status_code >= 401 && $response->status_code < 500) {
      throw new RemoteResourceClientError($decoded_body);

    } elseif ($response->status_code >= 500 && $response->status_code < 600) {
      throw new RemoteResourceServerError($decoded_body);

    } else {
      throw new RemoteResourceConnectionError($decoded_body, "Unknown response code");
    }
  }
}
