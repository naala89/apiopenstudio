<?php

use Slim\Container;
use Slim\Views\TwigExtension;
use Slim\Http\Uri;
use Slim\Views\Twig;
use Datagator\Admin\Controllers\CtrlUser;
use Datagator\Admin\Controllers\CtrlApplication;
use Datagator\Admin\Controllers\CtrlLogin;
use Datagator\Admin\Controllers\CtrlHome;

$container = $app->getContainer();

/**
 * Register Twig View helper.
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

  $twig = new Twig($viewPath, [
    'cache' => $settings['twig']['cache_enabled'] ? $settings['twig']['cache_path'] : FALSE,
  ]);

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
  return new CtrlHome($dbSettings, $view);
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
  return new CtrlLogin($dbSettings, $view);
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
  $view = $container->get('view');
  return new CtrlApplication($dbSettings, $view);
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
  $view = $container->get('view');
  return new CtrlUser($dbSettings, $mailSettings, $view);
};
