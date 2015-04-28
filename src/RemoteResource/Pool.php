<?php

namespace RemoteResource;

/**
 * Resource Pools must implement this interface
 */
interface Pool {
  
  /**
   * Get instance specific to $class_name
   * @param  string $class_name
   * @return mixed
   */
  public static function getInstance( $class_name );

}
