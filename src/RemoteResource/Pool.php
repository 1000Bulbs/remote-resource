<?php
namespace RemoteResource;

interface Pool {
  public static function getInstance( $class_name );
}
