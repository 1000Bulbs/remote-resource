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

    $this->local_log = new Monolog($app_name);
    $this->local_log->pushHandler(new RotatingFileHandler($log_path));
  }

  public function warning($msg) {
    $this->local_log->addWarning($msg);
  }

  public function error($msg) {
    if ($this->available()) {
      $this->new_relic->addError($msg);
    }
  }

  public function info($msg) {
    $this->local_log->addInfo($msg);
  }

  public function debug($msg) {
    $this->local_log->addDebug($msg);
  }

  public function notice($msg) {
    $this->local_log->addNotice($msg);
  }

  public function critical($msg) {
    if ($this->available()) {
      $this->new_relic->addCritical($msg);
    }
  }

  public function alert($msg) {
    if ($this->available()) {
      $this->new_relic->addAlert($msg);
    }
  }

  public function emergency($msg) {
    if ($this->available()) {
      $this->new_relic->addEmergency($msg);
    }
  }

  private function available() {
    return $this->new_relic ? true : false;
  }

}
