<?php

require_once dirname(__DIR__) . '/../vendor/autoload.php';

use Datagator\Admin\User;

$dir_templates = dirname(__DIR__) . '/admin/templates';
$dir_cache = dirname(__DIR__) . '/../../twig_cache';
$loader = new Twig_Loader_Filesystem($dir_templates);
//$twig = new Twig_Environment($loader, array(
//  'cache' => $dir_cache,
//));
$twig = new Twig_Environment($loader);

$user = new User();
if (!$user->isLoggedIn()) {
  $menu = ['Login' => '/admin/login'];
  $template = $twig->load('login.html');
  echo $template->render(['menu' => $menu]);
  exit;
}

$menu = [
  'Login' => '/login',
  'Accounts' => '/accounts',
  'Resources' => '/resources',
  'Users' => '/users'];
$template = $twig->load('page.html');

