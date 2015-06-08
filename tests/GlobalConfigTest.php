<?php

use RemoteResource\GlobalConfig;

class GlobalConfigTest extends PHPUnit_Framework_TestCase {

  public function testGlobalConfig_configs() {
    GlobalConfig::setAppName('ACME Co');
    GlobalConfig::setLogPath('/var/apps/acme_co/shared/');

    $this->assertEquals('ACME Co', GlobalConfig::appName());
    $this->assertEquals('/var/apps/acme_co/shared/', GlobalConfig::logPath());
  }
}
