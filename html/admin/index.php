<?php

require_once dirname(__DIR__) . '/../vendor/autoload.php';

$dir_templates = dirname(__DIR__) . '/admin/templates';
$dir_cache = dirname(__DIR__) . '/../../twig_cache';
$loader = new Twig_Loader_Filesystem($dir_templates);
//$twig = new Twig_Environment($loader, array(
//  'cache' => $dir_cache,
//));
$twig = new Twig_Environment($loader);

$username = !empty($_POST['username']) ? $_POST['username'] : (!empty($_SESSION['username']) ? $_SESSION['username'] : '');
$password = !empty($_POST['password']) ? $_POST['password'] : (!empty($_SESSION['password']) ? $_SESSION['password'] : '');
if (empty($username) || empty($password)) {
  echo $twig->render('login.html');
  exit;
} else {
  var_dump($_GET['q']);
  exit;
}


//$http = new GuzzleHttp\Client();

$template = $twig->load('page.html');


echo $template->render(array('the' => 'variables', 'go' => 'here'));
