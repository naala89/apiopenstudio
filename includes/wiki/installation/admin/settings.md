Settings
========

Copy `settings.example.yml` to `example.yml`, and then edit the settings
in `example.yml`

* debug.handlers.*.level
    * Set to the debug verbosity that you require.
* debug.loggers.*.handlers
    * Add "console" to the array if you want console output.
* admin
    * url
        * The url for admin
    * api_url
        * The Url for the ApiOpenStudio API
    * protocols
        * Accepted protocols to the API
    * core_account
        * The name of the core account
    * core_application
        * The name of the core application
    * base_path
        * The full path to the admin codebase
    * dir_tmp
        * the full path to the server temp directory
    * dir_public
        * path to the public directory, relative to `base_path`
    * pagination_step
        * The number of entries on listing pages.
    * slim.displayErrorDetails
        * Display Slim errors on the page. This should be set to false in
          production.
    * slim.determineRouteBeforeAppMiddleware.
        * This should not be altered.
* twig
    * options.cache_enabled
        * Turn on twig cache.
    * options.cache_path
        * The relative path from docroot to the twig cache directory. This
          should not be altered.
    * options.debug
        * Turn on twig debug mode. This should not be set to true on production.
    * template_path
        * The relative path to admin templates from `base_path`. This should
          not be altered.
