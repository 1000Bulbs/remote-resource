<?php
use RemoteResource\Config;
use RemoteResource\Connection;

class ConnectionTest extends PHPUnit_Framework_TestCase
{
  protected $config, $connection, $client;

  protected function setUp()
  {
    $this->config = new RemoteResource\Config(
      $format      = 'json',
      $auth_type   = 'basic',
      $credentials = 'user:password'
    );

    $this->connection = new RemoteResource\Connection($this->config);
    $this->connection->setClient(new MockClient());

    $this->client = $this->connection->client();
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

  // 400
  public function testGet_400() {
    $this->client->setResponseParams(400);

    $this->setExpectedException('RemoteResource\Exception\BadRequest');

    $this->connection->get('');
  }

  // 401
  public function testGet_401() {
    $this->client->setResponseParams(401);

    $this->setExpectedException('RemoteResource\Exception\UnauthorizedAccess');

    $this->connection->get('');
  }

  // 403
  public function testGet_403() {
    $this->client->setResponseParams(403);

    $this->setExpectedException('RemoteResource\Exception\ForbiddenAccess');

    $this->connection->get('');
  }

  // 404
  public function testGet_404() {
    $this->client->setResponseParams(404);

    $this->setExpectedException('RemoteResource\Exception\ResourceNotFound');

    $this->connection->get('');
  }

  // 405
  public function testGet_405() {
    $this->client->setResponseParams(405);

    $this->setExpectedException('RemoteResource\Exception\MethodNotAllowed');

    $this->connection->get('');
  }

  // 408
  public function testGet_408() {
    $this->client->setResponseParams(408);

    $this->setExpectedException('RemoteResource\Exception\RequestTimeout');

    $this->connection->get('');
  }

  // 409
  public function testGet_409() {
    $this->client->setResponseParams(409);

    $this->setExpectedException('RemoteResource\Exception\ResourceConflict');

    $this->connection->get('');
  }

  // 410
  public function testGet_410() {
    $this->client->setResponseParams(410);

    $this->setExpectedException('RemoteResource\Exception\ResourceGone');

    $this->connection->get('');
  }

  // 422
  public function testGet_422() {
    $this->client->setResponseParams(422);

    $this->setExpectedException('RemoteResource\Exception\ResourceInvalid');

    $this->connection->get('');
  }

  // 480
  public function testGet_480() {
    $this->client->setResponseParams(480);

    $this->setExpectedException('RemoteResource\Exception\ClientError');

    $this->connection->get('');
  }

  // 510
  public function testGet_510() {
    $this->client->setResponseParams(510);

    $this->setExpectedException('RemoteResource\Exception\ServerError');

    $this->connection->get('');
  }

  // 800
  public function testGet_800() {
    $this->client->setResponseParams(800);

    $this->setExpectedException('RemoteResource\Exception\ConnectionError');

    $this->connection->get('');
  }
}
