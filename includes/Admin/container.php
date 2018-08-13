<?php

use Slim\Container;
use Slim\Views\TwigExtension;
use Slim\Http\Uri;
use Slim\Views\Twig;
use Datagator\Admin\Controllers\Home;

/** @var \Slim\App $app */
$container = $app->getContainer();

/**
 * Register Twig View helper.
 *
 * @param Container $container
 *
 * @return \Slim\Views\Twig
 */
$container['view'] = function (Container $container) {
  $settings = $container->get('settings');
  $viewPath = $settings['twig']['path'];

  $twig = new Twig($viewPath, [
    'cache' => $settings['twig']['cache_enabled'] ? $settings['twig']['cache_path'] : FALSE
  ]);

  /** @var Twig_Loader_Filesystem $loader */
  $loader = $twig->getLoader();
  $loader->addPath($settings['public'], 'public');

  // Instantiate and add Slim specific extension.
  $router = $container->get('router');
  $uri = Uri::createFromEnvironment($container->get('environment'));
  $twig->addExtension(new TwigExtension($router, $uri));

  return $twig;
};

// Register Home controller.
$container['Home'] = function (Container $container) {
  $dbSettings = $container->get('settings')['db'];
  $view = $container->get('view');
  return new Home($dbSettings, $view);
};
