<?php

namespace RemoteResource;

use Monolog\Logger as Monolog;
use Monolog\Handler\NewRelicHandler;
use Monolog\Handler\RotatingFileHandler;

class Logger {

  private $new_relic, $local_log;

  public function __construct($app_name, $log_path) {
    if (extension_loaded('newrelic')) {
      $this->new_relic = new Monolog($app_name);
      $this->new_relic->pushHandler(new NewRelicHandler());
    }

    if ($log_path) {
      $this->local_log = new Monolog($app_name);
      $this->local_log->pushHandler(new RotatingFileHandler($log_path, 0, Monolog::DEBUG, true, 0777));
    }
  }

  public function warning($msg) {
    if ($this->localLogAvailable()) {
      $this->local_log->addWarning($msg);
    }
  }

  public function error($msg) {
    if ($this->newRelicAvailable()) {
      $this->new_relic->addError($msg);
    }
  }

  public function info($msg) {
    if ($this->localLogAvailable()) {
      $this->local_log->addInfo($msg);
    }
  }

  public function debug($msg) {
    if ($this->localLogAvailable()) {
      $this->local_log->addDebug($msg);
    }
  }

  public function notice($msg) {
    if ($this->localLogAvailable()) {
      $this->local_log->addNotice($msg);
    }
  }

  public function critical($msg) {
    if ($this->newRelicAvailable()) {
      $this->new_relic->addCritical($msg);
    }
  }

  public function alert($msg) {
    if ($this->newRelicAvailable()) {
      $this->new_relic->addAlert($msg);
    }
  }

  public function emergency($msg) {
    if ($this->newRelicAvailable()) {
      $this->new_relic->addEmergency($msg);
    }
  }

  private function newRelicAvailable() {
    return $this->new_relic ? true : false;
  }

  private function localLogAvailable() {
    return $this->local_log ? true : false;
  }

}
