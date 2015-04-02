<?php
use RemoteResource\Config;
use RemoteResource\Connection;

class ConnectionTest extends PHPUnit_Framework_TestCase
{
  public function testHeaders()
  {
    // set config in remote resource configuration
    $config = new RemoteResource\Config(
      $format      = 'json',
      $auth_type   = 'basic',
      $credentials = 'user:password'
    );

    $subject = new RemoteResource\Connection($config);

    $this->assertEquals(
      $subject->headers,
      array(
        'Content-Type' => 'application/json',
        'Authorization' => 'Basic '.base64_encode('user:password')
      )
    );
  }

  public function testGet_400() {
  }
}
