<?php

use Slim\Container;

/** @var \Slim\App $app */
$container = $app->getContainer();

// Register Twig View helper
$container['view'] = function (Container $container) {
  $settings = $container->get('settings');
  $viewPath = $settings['twig']['path'];

  $twig = new \Slim\Views\Twig($viewPath, [
    'cache' => $settings['twig']['cache_enabled'] ? $settings['twig']['cache_path'] : FALSE
  ]);

  /** @var Twig_Loader_Filesystem $loader */
  $loader = $twig->getLoader();
  $loader->addPath($settings['public'], 'public');

  // Instantiate and add Slim specific extension
  $router = $container->get('router');
  $uri = \Slim\Http\Uri::createFromEnvironment($container->get('environment'));
  $twig->addExtension(new \Slim\Views\TwigExtension($router, $uri));

  return $twig;
};
