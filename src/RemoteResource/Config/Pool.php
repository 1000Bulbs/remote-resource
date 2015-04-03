<?php
namespace RemoteResource\Config;
use RemoteResource\Config;

class Pool {
  private static $instances = array();

  public static function getConfig( $class_name ) {
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
