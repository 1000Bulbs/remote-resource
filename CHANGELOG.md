#Changelog

###[0.2.2] - 2015-04-14
- [bug] fixed a formatting issue where a response was being json encoded, then json encoded again within Guzzle

###[0.2.1] - 2015-04-02
- requests library replaced with guzzle
- concept of Formatters introduced
- Config and Connection APIs turned from class-level to instance-level
- configuration is now set at the RemoteResource subclass level, giving full control over payload data type and authentication concerns
- groundwork laid for easily adding additional supported configurations
- custom method support

###[0.2.0] - 2015-04-01
- php namespace pattern applied
- test data pulled out of library
- fully transitioned from codeception to phpunit
- bootstrapping for phpunit
- dependencies now all declared
- more obvious class names

###[0.1.0] - 2015-04-01
- RemoteResource and RemoteResourceCollection fully functional and fully tested
