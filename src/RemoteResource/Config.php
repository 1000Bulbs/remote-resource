<?php
namespace RemoteResource;
use RemoteResource\Formatter\Json;
use RemoteResource\Connection\HeaderCollection;
use RemoteResource\Connection\Header;

class Config {
  private $auth_type, 
          $credentials, 
          $format, 
          $formatter, 
          $headers,
          $supported_auth_types = array('basic', 'none', 'api_key'),
          $supported_formats    = array('json'),
          $default_auth_type    = 'none',
          $default_format       = 'json';

  /**
   * @param string $format      ex. "json"
   * @param string $auth_type   ex. "none"
   * @param string $credentials ex. "user:pwd"
   */
  public function __construct($format = null, $auth_type = null, $credentials = null) {
    $this->setFormat($format);
    $this->setAuthType($auth_type);
    $this->setCredentials($credentials);
    $this->setFormatter($this->format);
    $this->setHeaders();
  }

  /**
   * @return string
   */
  public function credentials() {
    if ( $this->auth_type == 'basic' ) {
      return self::base64EncodedCredentials();
    } else {
      return $this->credentials;
    }
  }

  /**
   * @return string
   */
  public function authType() {
    return $this->auth_type;
  }

  /**
   * @return string
   */
  public function format() {
    return $this->format;
  }

  /**
   * @return Formatter
   */
  public function formatter() {
    return $this->formatter;
  }

  /**
   * @return HeaderCollection
   */
  public function headers() {
    return $this->headers;
  }

  // ----------------------------
  // _____ PRIVATE METHODS ______
  // ____________________________

  /**
   * Setter, currently only basic or none are supported
   * @param  string     $auth_type authentication type to use, IE basic/none/oauth/etc
   * @throws \Exception            if the authentication type passed is not supported
   */
  private function setAuthType($auth_type = null) {
    if (is_null($auth_type)) {
      $this->auth_type = $this->default_auth_type;

    } elseif (in_array($auth_type, $this->supported_auth_types)) {
      $this->auth_type = $auth_type;

    } else {
      throw new \Exception('auth type not available');
    }
  }

  /**
   * Setter, currently only JSON is supported
   * @param  string     $format format to use, IE JSON/XML/etc
   * @throws \Exception         if the format passed is not supported
   */
  private function setFormat($format = null) {
    if (is_null($format)) {
      $this->format = $this->default_format;

    } elseif (in_array($format, $this->supported_formats)) {
      $this->format = $format;

    } else {
      throw new \Exception('format type '.$this->format.' not supported');
    }
  }

  /**
   * Setter, currently only JSON is supported
   * @param  string     $format formatter to use, IE JSON/XML/etc
   * @throws \Exception         if the formatter passed is not supported
   */
  private function setFormatter($format) {
    if ( $format == 'json' ) {
      $this->formatter = new Formatter\Json;

    } else {
      throw new \Exception('format type '.$this->format.' not supported');
    }
  }

  /**
   * Placeholder, basic auth and content-type json for now
   */
  private function setHeaders() {
    $headers= new HeaderCollection();

    if ($this->format() == 'json') {
      $headers->add( new Header('Content-Type', 'application/json') );
    }

    if ($this->authType() == 'basic') {
      $headers->add( new Header('Authorization', $this->credentials()) );
    } elseif ($this->authType() == 'api_key') {
      $headers->add( new Header('X-API-KEY', $this->credentials()) );
    }

    $this->headers = $headers;
  }

  /**
   * Setter
   * @param mixed $credentials ex. "user:pwd"
   */
  private function setCredentials($credentials = null) {
    $this->credentials = $credentials;
  }

  /**
   * Placeholder, basic auth for now
   * @return string
   */
  private function base64EncodedCredentials() {
    return 'Basic '. base64_encode($this->credentials);
  }
}
