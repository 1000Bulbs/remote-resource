<?php
namespace RemoteResource;

class Collection implements \Iterator {
  private $_collection = array();

  public function __construct($resource_instance, $response) {
    $resource_class = get_class($resource_instance);

     foreach ($response[$resource_class::pluralResourceName()] as $attributes) {
       $resource = Builder::buildForCollection(new $resource_class, $attributes);
       array_push($this->_collection, $resource);
     }
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
}
