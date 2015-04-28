<?php

namespace RemoteResource\Connection;

use RemoteResource\Connection\Header;

class HeaderCollection implements \Iterator, \Countable {
  private $_collection = array();

  /**
   * @param array|null $headers array of RemoteResource\Connection\Header objects
   */
  public function __construct(array $headers = null) {
    if (!$headers || empty($headers)) return;

    foreach ($headers as $header) {
      if (!is_a($header, 'Header')) throw new \Exception("Must be an instance of RemoteResource\Connection\Header");
      $this->_collection[] = $header;
    }
  }

  public function add(Header $header) {
    $this->_collection[] = $header;
  }

  public function rewind() {
    return reset($this->_collection);
  }

  public function current() {
    return current($this->_collection);
  }

  public function key() {
    return key($this->_collection);
  }

  public function next() {
    return next($this->_collection);
  }

  public function valid() {
    return key($this->_collection) !== null;
  }

  public function size() {
    return count($this->_collection);
  }

  public function count() {
    return $this->size();
  }

  public function first() {
    return (empty($this->_collection) ? null : $this->_collection[0]);
  }

  public function last() {
    return (empty($this->_collection) ? null : $this->_collection[count($this->_collection)-1]);
  }

  /**
   * This is needed to convert the HeaderCollection into a guzzle-consumable array of headers
   * @return array
   */
  public function toArray() {
    $array = array();
    foreach ($this->_collection as $header) {
      $array[$header->key] = $header->value; 
    }

    return $array;
  }
}
