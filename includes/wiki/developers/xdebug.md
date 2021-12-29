Configure Xdebug in PhpStorm
============================

The following assumes that you are using
[apiopenstudio_docker_dev](https://gitlab.com/apiopenstudio/apiopenstudio_docker_dev).

Enable Xdebug in docker
-----------------------

The dockerfile is configured toi create a build from ```php:n.n-fpm```,
and you can enable the build to include the xDebug packages using a simple
environment variable,

Turn on Xdebug in ```.env``` in your checkout of
[apiopenstudio_docker_dev](https://gitlab.com/apiopenstudio/apiopenstudio_docker_dev):

    WITH_XDEBUG=true

Rebuild your docker containers

    docker-compose build

Or

    docker-compose up -d --build

PHPStorm configurations
-----------------------

### Set the port

In PhpStorm, go to PhpStorm -> Preferences -> PHP -> Debug.

![Set xDebug port][set_xdebug_port]

### Configure a server.

This is how PHPStorm will map the local file paths to the ones in your
container.

Go to File -> Settings -> PHP -> Servers

![Configure the xDebug server][xdebug_server]

Give a name to the server. It should be a recognisable name, so you can identify
it later, i.e. "apiopenstudio".

Ensure you have "Use path mappings" checked, and edit the RHS to
```/var/www/html/api```.

Click "Apply" to save your configurations.

Configuring Xdebug for ApiOpenStudio Admin
------------------------------------------

Depending on how you have set up your projects, and you have admin in a
separate project, you will also need to set up servers as above, for your admin
project.

The remote paty mapping will be ```/var/www/html/admin```.

Links
-----

* [Setup Step Debugging in PHP with Xdebug 3 and Docker Compose][setup_step_debugging_php_xdebug3_docker]
* [Create a PHP debug server configuration][creating_a_php_debug_server_configuration]
* [Conditional COPY/ADD in Dockerfile?][conditional_copy_add_in_dockerfile]

[set_xdebug_port]: images/xdebug_port.png
[xdebug_server]: images/xdebug_server.png
[setup_step_debugging_php_xdebug3_docker]: https://matthewsetter.com/setup-step-debugging-php-xdebug3-docker
[creating_a_php_debug_server_configuration]: https://www.jetbrains.com/help/phpstorm/creating-a-php-debug-server-configuration.html
[conditional_copy_add_in_dockerfile]: https://www.py4u.net/discuss/1621084
