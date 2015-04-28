<?php
namespace RemoteResource\Pool;
use RemoteResource\Pool;
use RemoteResource\Config;

class ConfigPool implements Pool {
  private static $instances = array();

  public static function getInstance( $class_name ) {
    if (!array_key_exists($class_name, self::$instances)) {
      self::$instances[$class_name] = new Config(
        $class_name::$format,
        $class_name::$auth_type,
        $class_name::$credentials
      );
    }

    return self::$instances[$class_name];
  }
}
