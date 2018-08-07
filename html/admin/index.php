<?php

require_once dirname(__DIR__) . '/../vendor/autoload.php';
use Datagator\Admin\User;
use Datagator\Config;

Config::load();
session_start();

$loader = new Twig_Loader_Filesystem(Config::$adminTemplates);
//$twig = new Twig_Environment($loader, array(
//  'cache' => Config::$twigCache,
//));
$twig = new Twig_Environment($loader);

$user = new User();

if (isset($_POST['username']) || isset($_POST['password']) || isset($_POST['account'])) {
  $username = !empty($_POST['username']) ? $_POST['username'] : '';
  $password = !empty($_POST['password']) ? $_POST['password'] : '';
  $account = !empty($_POST['account']) ? $_POST['account'] : '';
  $result = $user->login($account, $username, $password);
  if (!$result) {
    $message['type'] = 'error';
    $message['text'] = 'Invalid username or password';
    $menu = ['Login' => '/admin/login'];
    $template = $twig->load('login.html');
    echo $template->render(['menu' => $menu, 'message' => $message]);
    exit;
  }
}

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

