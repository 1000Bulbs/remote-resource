<?php
require_once 'lib/BasicRemoteResource.php';

class BasicRemoteResourceTest extends \Codeception\TestCase\Test
{
  public function testHeaders()
  {
    $credentials = 'user:password';
    BasicRemoteResource::setCredentials($credentials);

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
