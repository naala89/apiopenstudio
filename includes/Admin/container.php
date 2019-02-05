<?php

use Slim\Container;
use Slim\Views\TwigExtension;
use Slim\Http\Uri;
use Slim\Views\Twig;
USE Slim\Flash\Messages;
use Gaterdata\Admin\Controllers\CtrlUser;
use Gaterdata\Admin\Controllers\CtrlApplication;
use Gaterdata\Admin\Controllers\CtrlAccount;
use Gaterdata\Admin\Controllers\CtrlLogin;
use Gaterdata\Admin\Controllers\CtrlHome;

$container = $app->getContainer();

/**
 * Flash container.
 *
 * @return \Slim\Flash\Messages
 */
$container['flash'] = function () {
  return new Messages();
};


/**
 * Register Twig View container.
 *
 * @param \Slim\Container $container
 *   Slim container.
 *
 * @return \Slim\Views\Twig
 *   Twig object.
 */
$container['view'] = function (Container $container) {
  $settings = $container->get('settings');
  $viewPath = $settings['twig']['path'];

  $twig = new Twig($viewPath, $settings['twig']['options']);

  $loader = $twig->getLoader();
  $loader->addPath($settings['public'], 'public');

  // Instantiate and add Slim specific extension.
  $router = $container->get('router');
  $uri = Uri::createFromEnvironment($container->get('environment'));
  $twig->addExtension(new TwigExtension($router, $uri));

  return $twig;
};

/**
 * Register Home controller.
 *
 * @param \Slim\Container $container
 *   Slim container.
 *
 * @return Datagator\Admin\Controllers\CtrlHome
 *   CtrlHome object.
 */
$container['CtrlHome'] = function (Container $container) {
  $dbSettings = $container->get('settings')['db'];
  $view = $container->get('view');
  $flash = $container->get('flash');
  return new CtrlHome($dbSettings, $view, $flash);
};

/**
 * Register Login controller.
 *
 * @param \Slim\Container $container
 *   Slim container.
 *
 * @return Datagator\Admin\Controllers\CtrlLogin
 *   CtrlLogin object.
 */
$container['CtrlLogin'] = function (Container $container) {
  $dbSettings = $container->get('settings')['db'];
  $view = $container->get('view');
  $flash = $container->get('flash');
  return new CtrlLogin($dbSettings, $view, $flash);
};

/**
 * Register Account controller.
 *
 * @param \Slim\Container $container
 *   Slim container.
 *
 * @return Datagator\Admin\Controllers\CtrlAccount
 *   CtrlApplication object.
 */
$container['CtrlAccount'] = function (Container $container) {
  $dbSettings = $container->get('settings')['db'];
  $paginationStep = $container->get('settings')['paginationStep'];
  $view = $container->get('view');
  $flash = $container->get('flash');
  return new CtrlAccount($dbSettings, $paginationStep, $view, $flash);
};

/**
 * Register Application controller.
 *
 * @param \Slim\Container $container
 *   Slim container.
 *
 * @return Datagator\Admin\Controllers\CtrlApplication
 *   CtrlApplication object.
 */
$container['CtrlApplication'] = function (Container $container) {
  $dbSettings = $container->get('settings')['db'];
  $paginationStep = $container->get('settings')['paginationStep'];
  $view = $container->get('view');
  $flash = $container->get('flash');
  return new CtrlApplication($dbSettings, $paginationStep, $view, $flash);
};

/**
 * Register User controller.
 *
 * @param \Slim\Container $container
 *   Slim container.
 *
 * @return Datagator\Admin\Controllers\CtrlUser
 *   CtrlUser object.
 */
$container['CtrlUser'] = function (Container $container) {
  $dbSettings = $container->get('settings')['db'];
  $mailSettings = $container->get('settings')['mail'];
  $paginationStep = $container->get('settings')['paginationStep'];
  $view = $container->get('view');
  $flash = $container->get('flash');
  return new CtrlUser($dbSettings, $mailSettings, $paginationStep, $view, $flash);
};
