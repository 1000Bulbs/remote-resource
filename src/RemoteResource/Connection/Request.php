<?php

namespace RemoteResource\Connection;

use RemoteResource\Connection\Header;
use RemoteResource\Config;

class Request {

  private $config, $headers;

  public function __construct(Config $config) {
    $this->config = $config;
    $this->headers = new HeaderCollection();

    $this->headers->add($this->createAuthorizationHeader());
    $this->headers->add($this->createContentTypeHeader());
  }

  public function getHeaders() {
    return $this->headers->toArray();
  }

  // ----------------------------
  // _____ PRIVATE METHODS ______
  // ____________________________

  private function createContentTypeHeader() {
    switch ( $this->config->format() ) {
      case 'json':
        $content_type = 'application/json';
        break;
      default:
        throw new \Exception('no header implemented for type '.$this->config->format());
    }

    return new Header('Content-Type', $content_type);
  }

  private function createAuthorizationHeader() {
    switch ( $this->config->format() ) {
    case 'json':
      return new Header('Authorization', $this->config->credentials());
      break;
    }
  }
}