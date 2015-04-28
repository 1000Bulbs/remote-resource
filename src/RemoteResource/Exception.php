<?php
namespace RemoteResource;

/**
 * RemoteResource's custom exception class, needed a method to store the response
 */
class Exception extends \Exception {
  public $response;

  /**
   * The exception stores the response for reference,
   * helps when handling errors.
   * 
   * @param mixed          $response the response object
   * @param string         $message  the exception message
   * @param integer        $code     an error code
   * @param Exception|null $previous usually null
   */
  public function __construct($response, $message = "", $code = 0, Exception $previous = null) {
    parent::__construct($message, $code, $previous);

    $this->response = $response;
  }
}
