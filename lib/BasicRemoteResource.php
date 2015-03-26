<?php
require_once 'Requests/Requests.php';
Requests::register_autoloader();

class BasicRemoteResource {

  // GET
  public function get($path) {
    return Requests::get($path, $this->headers());
  }

  // POST
  public function post($path, $hash_of_attributes = array()) {
    return Requests::post($path, $hash_of_attributes, $this->headers());
  }

  // PATCH
  public function patch($path, $hash_of_attributes = array()) {
    return Requests::patch($path, $hash_of_attributes, self::headers());
  }

  // DELETE
  public function delete($path) {
    return Requests::delete($path, self::headers());
  }

  public function site() {
    throw new Exception('Not implemented');
  }

  public function headers() {
    $encoded = base64_encode('user:password'); # TODO: refactor
    return array(
      'Content-Type' => 'application/json',
      'Authorization' => 'Basic '.$encoded
    );
  }
}
