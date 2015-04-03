<?php
class MockResponse {
  public $status_code, $body;

  public function getBody() {
    return $this->body;
  }

  public function getStatusCode() {
    return $this->status_code;
  }
}

class MockClient {
  private $response;

  public function createRequest($verb, $path, $headers, $body) {
    return $this;
  }

  public function setResponseParams($status_code, $body=array()) {
    $this->response = new MockResponse();
    $this->response->status_code = $status_code;
    $this->response->body = json_encode($body);
  }

  public function send() {
    return $this->response;
  }
}
