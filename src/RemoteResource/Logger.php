<?php

namespace RemoteResource;

use Monolog\Logger;
use Monolog\Handler\NewRelicHandler;

class Logger {

  private $log;

  public function __construct(string $app_name) {
    if (extension_loaded('newrelic')) {
      $this->log = new Logger($app_name);
      $this->log->pushHandler(new NewRelicHandler());
    }
  }

  public function warning(string $msg) {
    if (!$this->available()) {
      $this->log->addWarning($msg);
    }
  }

  public function error(string $msg) {
    if (!$this->available()) {
      $this->log->addError($msg);
    }
  }

  public function info(string $msg) {
    if (!$this->available()) {
      $this->log->addInfo($msg);
    }
  }

  public function debug(string $msg) {
    if (!$this->available()) {
      $this->log->addDebug($msg);
    }
  }

  public function notice(string $msg) {
    if (!$this->available()) {
      $this->log->addNotice($msg);
    }
  }

  public function critical(string $msg) {
    if (!$this->available()) {
      $this->log->addCritical($msg);
    }
  }

  public function alert(string $msg) {
    if (!$this->available()) {
      $this->log->addAlert($msg);
    }
  }

  public function emergency(string $msg) {
    if (!$this->available()) {
      $this->log->addEmergency($msg);
    }
  }

  private function available() {
    if (!isset($this->log)) {
      return false;
    }

    return true;
  }

}
