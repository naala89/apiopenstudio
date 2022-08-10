Configuring debug
=================

The codebase uses 2 logging streams:

* db
    * Logging from the DB.
* api
    * logging from the api.

Configuration is defined in `settings.yml`, using a wrapper for
`Mononlog`. The reason for the wrapper, is so that we can define the
`db` and `api` logging streams in the same object, and provide the same
configurability that `Mononlog` provides. The two logging streams can then
be confiured to use as many handlers and formatters as you wish.

Debugging configuration
-----------------------

This is done in the `debug` section in `settings.yml`.

### Formatters

A `default` formatter is provided in `debug.formatters`, but you can add
as many as you like. These define the log message layout.

### Handlers

Sample handlers are provided in `example.settings.yml`.

You can use a single handler (to send db and api logs to the same file or DB
table for example ), or multiple handlers if you want to send the logging to
multiple handlers.

#### Setting verbosity levels

In the `level` attribute, set the value to:

* DEBUG: Detailed debug information.
* INFO: Interesting events. Examples: User logs in, SQL logs.
* NOTICE: Normal but significant events.
* WARNING: Exceptional occurrences that are not errors. Examples: Use of
  deprecated APIs, poor use of an API, undesirable things that are not
  necessarily wrong.
* ERROR: Runtime errors that do not require immediate action but should
  typically be logged and monitored.
* CRITICAL: Critical conditions. Example: Application component
  unavailable, unexpected exception.
* ALERT: Action must be taken immediately. Example: Entire website down,
  database unavailable, etc. This should trigger the SMS alerts and wake you up.
* EMERGENCY: Emergency: system is unusable.

### Assigning handlers to logging streams

This is done the `debug.loggers` section. assign as many handlers as you
like to the `db` and `api` loggers.

Links
-----

* [Monolog documentation][monolog_documentation]

[monolog_documentation]: https://github.com/Seldaek/monolog
