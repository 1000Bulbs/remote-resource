#Changelog

###[0.2.12] - 2015-10-08
- set CURLOPT_FORBID_REUSE to 1. This will force connections to be closed once processing of a request is completed. Might help avoid over pooling. http://curl.haxx.se/libcurl/c/CURLOPT_FORBID_REUSE.html

###[0.2.11] - 2015-10-08
- set CURLOPT_TCP_NODELAY to 1. This should allow small packets to process quickly. http://curl.haxx.se/libcurl/c/CURLOPT_TCP_NODELAY.html

###[0.2.10] - 2015-06-08
- [bug] GlobalConfig setLogPath takes log_path param

###[0.2.9] - 2015-06-08
- [logging] Non-error logging now logs to the server.
- [feature] Global Configuration options now settable: app_name, log_path

###[0.2.8] - 2015-06-08
- [logging] Also logging guzzle exceptions.

###[0.2.7] - 2015-06-08
- [logging] Added global logging through monolog out to newrelic. Only activated if newrelic is configured against the application consuming RemoteResource.

###[0.2.6] - 2015-05-14
- [bug] Connection headers were not being transformed into a map. This meant that the Guzzle Client wasnt able to process the headers properly.

###[0.2.4] - 2015-04-29
- [bug] removed the setSite method on RemoteResource. The method was problematic because it is setting a static property against RemoteResource itself, which will overwrite the path globally, affecting all sub classes.
  From now on, we should not manipulate the static properties meant to be set against the sub-classes anywhere in the RemoteResource class.

###[0.2.3] - 2015-04-14
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
