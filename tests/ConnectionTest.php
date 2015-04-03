<?php
use RemoteResource\Config;
use RemoteResource\Connection;

class ConnectionTest extends PHPUnit_Framework_TestCase
{
  protected $config, $connection;

  protected function setUp()
  {
    $this->config = new RemoteResource\Config(
      $format      = 'json',
      $auth_type   = 'basic',
      $credentials = 'user:password'
    );

    $this->connection = new RemoteResource\Connection($this->config);
    $this->connection->setClient(new MockClient());
  }

  public function testHeaders()
  {

    $this->assertEquals(
      $this->connection->headers,
      array(
        'Content-Type' => 'application/json',
        'Authorization' => 'Basic '.base64_encode('user:password')
      )
    );
  }

  public function testGet_400() {
  }
}
