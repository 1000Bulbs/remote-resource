<?php

use RemoteResource\Config;
use RemoteResource\Connection;

class TimeoutTest extends PHPUnit_Framework_TestCase {

  protected $config, $connection, $client;

  protected function setUp() {
    $this->config = new RemoteResource\Config(
      $format      = 'json',
      $auth_type   = 'basic',
      $credentials = 'user:password'
    );

    $this->connection = new RemoteResource\Connection($this->config);
    // $this->connection->setClient(new MockClient());

    $this->client = $this->connection->client();
  }

  public function testHeaders() {
    $this->assertEquals(
      $this->connection->headers,
      array(
        'Content-Type' => 'application/json',
        'Authorization' => 'Basic '.base64_encode('user:password')
      )
    );
  }

  // Guzzle Exception of any kind
  public function testGet_GuzzleError() {
    $this->setExpectedException('RemoteResource\Exception\ConnectionError');
    $this->connection->get('');
  }
}
