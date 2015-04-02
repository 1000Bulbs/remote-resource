<?php
namespace RemoteResource\Connection;
use RemoteResource\Config;

class Header {
  private $headers = array();

  public function getHeaders() {
    if (empty($headers)) {
      // TODO: move auth determimation into config file
      // move json determination into formatter
      $credentials = Config::base64EncodedCredentials();
      $this->addHeader('Content-Type', 'application/json');
      $this->addHeader('Authorization', 'Basic '.$credentials);
    }

    return $this->headers;
  }

  public function addHeader($header_field_name, $value) {
    $this->headers[$header_field_name] = $value;
  }
}
