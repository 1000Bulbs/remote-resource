<?php

namespace RemoteResource\Connection;

class Header {
  public $key, $value;

  /**
   * @param string $key   ex. "Content-Type"
   * @param string $value ex. "application/json"
   */
  public function __construct($key, $value) {
    $this->key = $key;
    $this->value = $value;
  }
}
