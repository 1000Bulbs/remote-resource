<?php
require_once 'lib/BasicRemoteResource.php';

class BasicRemoteResourceTest extends PHPUnit_Framework_TestCase
{
  public function testHeaders()
  {
    // set credentials in remote resource configuration
    $credentials = 'user:password';
    RemoteResourceConfig::setCredentials($credentials);

    $subject = new BasicRemoteResource();

    $this->assertEquals(
      $subject->headers(),
      array(
        'Content-Type' => 'application/json',
        'Authorization' => 'Basic '.base64_encode('user:password')
      )
    );
  }
}
