Configuring debug levels
========================

By default, here are 2 logging streams configured by default:

* db
    * Logging from the DB.
* api
    * logging from the api.

Setting verbosity levels
------------------------

Configuration is defined separately in cascade.yml.

In handlers.console.level, set the value to:

* DEBUG (100): Detailed debug information.
* INFO (200): Interesting events. Examples: User logs in, SQL logs.
* NOTICE (250): Normal but significant events.
* WARNING (300): Exceptional occurrences that are not errors. Examples: Use of deprecated APIs, poor use of an API, undesirable things that are not necessarily wrong.
* ERROR (400): Runtime errors that do not require immediate action but should typically be logged and monitored.
* CRITICAL (500): Critical conditions. Example: Application component unavailable, unexpected exception.
* ALERT (550): Action must be taken immediately. Example: Entire website down, database unavailable, etc. This should trigger the SMS alerts and wake you up.
* EMERGENCY (600): Emergency: system is unusable.

To alter the log file location, edit handler.<name>.stream.

To add console output to the stream, add "console" to loggers.<name>.handlers array.

Links
-----
 * https://github.com/Seldaek/monolog/blob/master/doc/01-usage.md#installation