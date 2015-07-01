<?php

use RemoteResource\GlobalConfig;
use RemoteResource\Logger;
use Monolog\Logger as Monolog;

class GlobalConfigTest extends PHPUnit_Framework_TestCase {

  public function testGlobalConfig_configs() {
    GlobalConfig::setAppName('ACME Co');
    GlobalConfig::setLogPath('/var/apps/acme_co/shared/');

    $this->assertEquals('ACME Co', GlobalConfig::appName());
    $this->assertEquals('/var/apps/acme_co/shared/', GlobalConfig::logPath());


    GlobalConfig::setAppName('ACME Co');
    GlobalConfig::setLogPath('tmp.log');

    $logger = new Logger(GlobalConfig::appName(), GlobalConfig::logPath());
    $logger->debug("something has gone wrong.");
    $date_time = new \DateTime;
    $date_time = $date_time->format("Y-m-d");
    $file_name = "tmp-".$date_time.'.log';
    $permissions = substr(sprintf('%o', fileperms($file_name)), -4);

    // ensure permissions are properly set
    $this->assertEquals('0777', $permissions);
  }
}
