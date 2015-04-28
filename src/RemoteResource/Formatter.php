<?php

namespace RemoteResource;

/**
 * Formatters must implement this interface
 */
interface Formatter {

  /**
   * Format a request body
   * @param  array $request_body
   * @return string               formatted request body
   */
  public function formatRequest($request_body);

  /**
   * Format response body
   * @param  string $response_body
   * @return array                 formatted response body
   */
  public function formatResponse($response_body);

}
