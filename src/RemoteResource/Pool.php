<?php
namespace RemoteResource;

interface Pool {
  public function getInstance( $class_name );
}
