<?php
namespace RemoteResource;

class GlobalConfig {
  private static $app_name, $log_path;

  public static function appName() {
    return self::$app_name;
  }

  public static function logPath() {
    return self::$log_path;
  }

  public static function setAppName($app_name) {
    self::$app_name = $app_name;
  }

  public static function setLogPath() {
    self::$log_path = $log_path;
  }
}
