<?php
namespace RemoteResource\Connection;
use RemoteResource\Connection;
use RemoteResource\Config\Pool as ConfigPool;

class Pool {
  private static $instances = array();

  public static function getConnection( $class_name ) {
    if (!array_key_exists($class_name, self::$instances)) {
      self::$instances[$class_name] = new Connection(
        ConfigPool::getConfig( $class_name )
      );
    }

    return self::$instances[$class_name];
  }
}
