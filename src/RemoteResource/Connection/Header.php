<?php
namespace RemoteResource\Connection;
use RemoteResource\Config;

class Header {
  private $config, $headers = array();

  public function __construct(Config $config) {
    $this->config = $config;
  }

  public function getHeaders() {
    if (empty($headers)) {
      $this->addContentTypeHeader();
      $this->addAuthorizationHeader();
    }

    return $this->headers;
  }

  public function addHeader($header_field_name, $value) {
    $this->headers[$header_field_name] = $value;
  }

  // ----------------------------
  // _____ PRIVATE METHODS ______
  // ____________________________

  private function addContentTypeHeader() {
    switch ( $this->config->format() ) {
      case 'json':
        $content_type = 'application/json';
        break;
      default:
        throw new \Exception('no header implemented for type '.$this->config->format());
    }

    $this->addHeader('Content-Type', $content_type);
  }

  private function addAuthorizationHeader() {
    switch ( $this->config->format() ) {
    case 'json':
      $this->addHeader('Authorization', $this->config->credentials());
      break;
    }
  }
}
