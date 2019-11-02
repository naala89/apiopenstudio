<?php

use Slim\Container;
use Slim\Views\TwigExtension;
use Slim\Http\Uri;
use Slim\Views\Twig;
USE Slim\Flash\Messages;
use Gaterdata\Admin\Controllers\CtrlUser;
use Gaterdata\Admin\Controllers\CtrlUsers;
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
  $viewDir = $settings['api']['base_path'] . $settings['twig']['template_path'];
  $publicDir = $settings['api']['base_path'] . $settings['api']['public_path'];

  $twig = new Twig($viewDir, $settings['twig']['options']);
  $loader = $twig->getLoader();
  $loader->addPath($publicDir, 'public');

  // Instantiate and add twig extension/s.
  $router = $container->get('router');
  $uri = Uri::createFromEnvironment($container->get('environment'));
  $twig->addExtension(new TwigExtension($router, $uri));
  if ($settings['twig']['options']['debug']) {
    $twig->addExtension(new Twig_Extension_Debug());
  }

  return $twig;
};

/**
 * Register Login controller.
 *
 * @param \Slim\Container $container
 *   Slim container.
 *
 * @return Gaterdata\Admin\Controllers\CtrlLogin
 *   CtrlLogin object.
 */
$container['CtrlLogin'] = function (Container $container) {
  $settings = $container->get('settings');
  $view = $container->get('view');
  $flash = $container->get('flash');
  return new CtrlLogin($settings, $view, $flash);
};

/**
 * Register Home controller.
 *
 * @param \Slim\Container $container
 *   Slim container.
 *
 * @return Gaterdata\Admin\Controllers\CtrlHome
 *   CtrlHome object.
 */
$container['CtrlHome'] = function (Container $container) {
  $settings = $container->get('settings');
  $view = $container->get('view');
  $flash = $container->get('flash');
  return new CtrlHome($settings, $view, $flash);
};

/**
 * Register Account controller.
 *
 * @param \Slim\Container $container
 *   Slim container.
 *
 * @return Gaterdata\Admin\Controllers\CtrlAccount
 *   CtrlApplication object.
 */
$container['CtrlAccount'] = function (Container $container) {
  $settings = $container->get('settings');
  $view = $container->get('view');
  $flash = $container->get('flash');
  return new CtrlAccount($settings, $view, $flash);
};

/**
 * Register Application controller.
 *
 * @param \Slim\Container $container
 *   Slim container.
 *
 * @return Gaterdata\Admin\Controllers\CtrlApplication
 *   CtrlApplication object.
 */
$container['CtrlApplication'] = function (Container $container) {
  $settings = $container->get('settings');
  $view = $container->get('view');
  $flash = $container->get('flash');
  return new CtrlApplication($settings, $view, $flash);
};

/**
 * Register Users controller.
 *
 * @param \Slim\Container $container
 *   Slim container.
 *
 * @return Gaterdata\Admin\Controllers\CtrlUsers
 *   CtrlUser object.
 */
$container['CtrlUsers'] = function (Container $container) {
  $settings = $container->get('settings');
  $view = $container->get('view');
  $flash = $container->get('flash');
  return new CtrlUsers($settings, $view, $flash);
};

/**
 * Register User controller.
 *
 * @param \Slim\Container $container
 *   Slim container.
 *
 * @return Gaterdata\Admin\Controllers\CtrlUser
 *   CtrlUser object.
 */
$container['CtrlUser'] = function (Container $container) {
  $settings = $container->get('settings');
  $view = $container->get('view');
  $flash = $container->get('flash');
  return new CtrlUser($settings, $view, $flash);
};
