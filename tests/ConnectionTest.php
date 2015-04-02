<?php
use RemoteResource\Config;
use RemoteResource\Connection;

class ConnectionTest extends PHPUnit_Framework_TestCase
{
  public function testHeaders()
  {
    // set credentials in remote resource configuration
    $credentials = 'user:password';
    RemoteResource\Config::setCredentials($credentials);

    $subject = new RemoteResource\Connection();

    $this->assertEquals(
      $subject->headers(),
      array(
        'Content-Type' => 'application/json',
        'Authorization' => 'Basic '.base64_encode('user:password')
      )
    );
  }
}
