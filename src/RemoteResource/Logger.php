<?php

namespace RemoteResource;

use Monolog\Logger as Monolog;
use Monolog\Handler\NewRelicHandler;

class Logger {

  private $log;

  public function __construct($app_name) {
    if (extension_loaded('newrelic')) {
      $this->log = new Monolog($app_name);
      $this->log->pushHandler(new NewRelicHandler());
    }
  }

  public function warning($msg) {
    if ($this->available()) {
      $this->log->addWarning($msg);
    }
  }

  public function error($msg) {
    if ($this->available()) {
      $this->log->addError($msg);
    }
  }

  public function info($msg) {
    if ($this->available()) {
      $this->log->addInfo($msg);
    }
  }

  public function debug($msg) {
    if ($this->available()) {
      $this->log->addDebug($msg);
    }
  }

  public function notice($msg) {
    if ($this->available()) {
      $this->log->addNotice($msg);
    }
  }

  public function critical($msg) {
    if ($this->available()) {
      $this->log->addCritical($msg);
    }
  }

  public function alert($msg) {
    if ($this->available()) {
      $this->log->addAlert($msg);
    }
  }

  public function emergency($msg) {
    if ($this->available()) {
      $this->log->addEmergency($msg);
    }
  }

  private function available() {
    return $this->log ? true : false;
  }

}
