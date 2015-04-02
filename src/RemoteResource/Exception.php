<?php
namespace RemoteResource;

class Exception extends \Exception {
  public $response;

  public function __construct($response, $message = "", $code = 0, Exception $previous = null) {
    parent::__construct($message, $code, $previous);

    $this->response = $response; 
  }
}
