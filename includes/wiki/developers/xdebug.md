Configure Xdebug in PhpStorm
============================

The following assumes that you are using
[ApiOpenStudio Docker Dev][docker_dev].

Enable Xdebug in your docker image
----------------------------------

The dockerfile is configured toi create a build from `php:n.n-fpm`, and you can
enable the build to include the xDebug packages using a simple environment
variable,

Turn on Xdebug in `.env` in your checkout of
[ApiOpenStudio Docker Dev][docker_dev]:

    WITH_XDEBUG=true

Rebuild your docker containers

    $ docker-compose build

Or

    $ docker-compose up -d --build

PHPStorm configurations
-----------------------

### PHP language level and CLI interpreter

If this is the first time that xDebug has been set up in your PHPStorm, you
need to set up the PHP interpreters.

* Ensure that your docker instance is running, so that PHPStorm can detect the
  `apiopenstudio-php` container.
* Click on Preferences -> PHP.
* Select the `PHP language level` - this should be the same as the PHP version
  in your docker setup.

  ![PHP language level][xdebug_set_php_language_level]
* In the CLI interpreter, click on the `...`.
* In the `CLI Interpreters` modal that pops up, click on the `+` to add a new
  interpreter and select `From Docker, Vagrant, VM, WSL, Remote...`.

  ![Select CLI interpreter][xdebug_select_cli_interpreter]
* In the new popup:

  ![xdebug_configure_cli_interpreter][xdebug_configure_cli_interpreter]
  * Select `Docker`.
  * Image name: `apiopenstudio_docker_dev_php`.
  * Leave the `PHP interpreter path` as `php`.
  * Click on `Apply` and `OK`.

![CLI interpreters][xdebug_cli_interpreters]

### Set the port

In PhpStorm, go to PhpStorm -> Preferences -> PHP -> Debug.

![Set xDebug port][xdebug_set_port]

### Configure a server.

This is how PHPStorm will map the local file paths to the ones in your
container.

Go to File -> Settings -> PHP -> Servers

![Configure the xDebug server][xdebug_server]

Give a name to the server. It should be a recognisable name, so you can identify
it later, i.e. "apiopenstudio".

Ensure you have "Use path mappings" checked, and edit the RHS to
`/var/www/html/api`.

Click "Apply" to save your configurations.

Configuring Xdebug for ApiOpenStudio Admin
------------------------------------------

Depending on how you have set up your projects, and you have admin in a
separate project, you will also need to set up servers as above, for your admin
project.

The remote path mapping will be `/var/www/html/admin`.

Links
-----

* [ApiOpenStudio Docker Dev][docker_dev]
* [JetBrains configure xDebug][jetbrains_configure_xdebug]
* [Setup Step Debugging in PHP with Xdebug 3 and Docker Compose][setup_step_debugging_php_xdebug3_docker]
* [Create a PHP debug server configuration][creating_a_php_debug_server_configuration]
* [Conditional COPY/ADD in Dockerfile?][conditional_copy_add_in_dockerfile]

[docker_dev]: [https://gitlab.com/apiopenstudio/apiopenstudio_docker_dev]
[jetbrains_configure_xdebug]: https://www.jetbrains.com/help/phpstorm/configuring-xdebug.html
[setup_step_debugging_php_xdebug3_docker]: https://matthewsetter.com/setup-step-debugging-php-xdebug3-docker
[creating_a_php_debug_server_configuration]: https://www.jetbrains.com/help/phpstorm/creating-a-php-debug-server-configuration.html
[conditional_copy_add_in_dockerfile]: https://www.py4u.net/discuss/1621084
[xdebug_set_php_language_level]: images/xdebug_php_language_level.png
[xdebug_select_cli_interpreter]: images/xdebug_select_cli_interpreter.png
[xdebug_configure_cli_interpreter]: images/xdebug_configure_cli_interpreter.png
[xdebug_cli_interpreters]: images/xdebug_cli_interpreters.png
[xdebug_set_port]: images/xdebug_port.png
[xdebug_server]: images/xdebug_server.png
