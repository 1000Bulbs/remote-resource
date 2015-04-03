<?php
namespace RemoteResource;

interface Formatter {
  public function formatRequest($request_body);
  public function formatResponse($response_body);
}
