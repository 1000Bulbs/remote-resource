<?php
namespace RemoteResource\Pool;
use RemoteResource\Pool;
use RemoteResource\Connection;
use RemoteResource\Pool\ConfigPool;

class ConnectionPool implements Pool {
  private static $instances = array();

  public static function getInstance( $class_name ) {
    if (!array_key_exists($class_name, self::$instances)) {
      self::$instances[$class_name] = new Connection(
        ConfigPool::getInstance( $class_name )
      );
    }

    return self::$instances[$class_name];
  }
}
