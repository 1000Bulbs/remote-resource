<?php
namespace RemoteResource\Formatter;
use RemoteResource\Formatter;

class Json implements Formatter {
  public function format($request_body) {
    return json_encode($request_body);
  }
}
