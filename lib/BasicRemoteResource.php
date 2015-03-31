<?php
require_once 'Requests/Requests.php';
Requests::register_autoloader();

class BasicRemoteResource {

  // GET
  public static function get($path) {
    return Requests::get($path, self::headers());
  }

  // POST
  public static function post($path, $hash_of_attributes = array()) {
    return Requests::post($path, self::headers(), json_encode($hash_of_attributes));
  }

  // PATCH
  public static function patch($path, $hash_of_attributes = array()) {
    return Requests::patch($path, self::headers(), json_encode($hash_of_attributes));
  }

  // DELETE
  public static function delete($path) {
    return Requests::delete($path, self::headers());
  }

  public static function headers() {
    $encoded = base64_encode('user:password'); # TODO: refactor
    return array(
      'Content-Type' => 'application/json',
      'Authorization' => 'Basic '.$encoded
    );
  }
}
