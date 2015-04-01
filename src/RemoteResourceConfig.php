<?php

class RemoteResourceConfig {
  private static $credentials;

  public static function setCredentials($credentials) {
    self::$credentials = $credentials;
  }

  public static function credentials() {
    return self::$credentials;
  }

  public static function base64EncodedCredentials() {
    return base64_encode(self::$credentials);
  }
}
