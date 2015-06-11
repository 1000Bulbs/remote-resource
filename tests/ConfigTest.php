<?php
use RemoteResource\Config;

class ConfigTest extends PHPUnit_Framework_TestCase {

  public function testAuthType_none() {
    $config = new RemoteResource\Config(null, 'none');

    $this->assertEquals($config->authType(), 'none');
    $this->assertEquals($config->credentials(), null);
  }

  public function testAuthType_basic() {
    $config = new RemoteResource\Config(null, 'basic', 'name:password');

    $this->assertEquals($config->authType(), 'basic');
    $this->assertEquals($config->credentials(), 'Basic '.base64_encode('name:password'));
  }

  public function testAuthType_apiKey() {
    $config = new RemoteResource\Config(null, 'api_key', '1234ASDF1234ASDF1234ASDF1234ASDF');

    $this->assertEquals($config->authType(), 'api_key');
    $this->assertEquals($config->credentials(), '1234ASDF1234ASDF1234ASDF1234ASDF');
  }

  public function testAuthType_default() {
    $config = new RemoteResource\Config();

    $this->assertEquals($config->authType(), 'none');
    $this->assertEquals($config->credentials(), null);
  }

  public function testFormat_json() {
    $config = new RemoteResource\Config('json');

    $this->assertEquals($config->format(), 'json');
    $this->assertInstanceOf('RemoteResource\Formatter\Json', $config->formatter());
  }

  public function testFormat_default() {
    $config = new RemoteResource\Config();

    $this->assertEquals($config->format(), 'json');
    $this->assertInstanceOf('RemoteResource\Formatter\Json', $config->formatter());
  }
}
