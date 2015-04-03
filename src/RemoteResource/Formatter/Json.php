<?php
namespace RemoteResource\Formatter;
use RemoteResource\Formatter;

class Json implements Formatter {
  public function formatRequest($request_body) {
    return json_encode($request_body);
  }

  public function formatResponse($response_body) {
    return json_decode($response_body, true);
  }
}
