<?php

namespace RemoteResource\Connection;

class Header {
  public $key, $value;

  public function __construct($key, $value) {
    $this->key = $key;
    $this->value = $value;
  }
}
