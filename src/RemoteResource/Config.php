<?php
namespace RemoteResource;
use RemoteResource\Formatter\Json;

class Config {
  private $auth_type, $credentials, $format, $formatter,
          $supported_auth_types = array('basic', 'none'),
          $supported_formats    = array('json');

  public function __construct($format, $auth_type, $credentials) {
    $this->setFormat($format);
    $this->setAuthType($auth_type);
    $this->setCredentials($credentials);
    $this->setFormatter($format);
  }

  public function credentials() {
    if ( $this->auth_type == 'basic' ) {
      return self::base64EncodedCredentials();
    } else {
      return $this->credentials;
    }
  }

  public function authType() {
    return $this->auth_type;
  }

  public function format() {
    return $this->format;
  }

  public function formatter() {
    return $this->formatter;
  }

  // ----------------------------
  // _____ PRIVATE METHODS ______
  // ____________________________

  private function setAuthType($auth_type) {
    if (in_array($auth_type, $this->supported_auth_types)) {
      $this->auth_type = $auth_type;
    } else {
      throw new \Exception('auth type not available');
    }
  }

  private function setFormat($format) {
    if (in_array($format, $this->supported_formats)) {
      $this->format = $format;
    } else {
      throw new \Exception('format type '.$this->format.' not supported');
    }
  }

  private function setFormatter($format) {
    if ( $format == 'json' ) {
      $this->formatter = new Formatter\Json;
    } else {
      throw new \Exception('format type '.$this->format.' not supported');
    }
  }

  private function setCredentials($credentials) {
    $this->credentials = $credentials;
  }

  private function base64EncodedCredentials() {
    return 'Basic '. base64_encode($this->credentials);
  }
}
