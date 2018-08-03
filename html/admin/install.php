<?php

require_once dirname(__DIR__) . '/../vendor/autoload.php';
use Datagator\Config;

Config::load();

$step = isset($_GET['step']) ? $_GET['step'] : 1;

$dsnOptions = '';
if (sizeof(Config::$dboptions) > 0) {
  foreach (Config::$dboptions as $k => $v) {
    $dsnOptions .= sizeof($dsnOptions) == 0 ? '?' : '&';
    $dsnOptions .= "$k=$v";
  }
}

$dir_templates = dirname(__DIR__) . '/admin/templates';
$dir_cache = dirname(__DIR__) . '/../../twig_cache';
$loader = new Twig_Loader_Filesystem($dir_templates);
//$twig = new Twig_Environment($loader, array(
//  'cache' => $dir_cache,
//));
$twig = new Twig_Environment($loader);
$template = $twig->load('install.html');

$dsnOptions = sizeof(Config::$dboptions) > 0 ? '?'.implode('&', Config::$dboptions) : '';
$dsn = Config::$dbdriver . '://' . Config::$dbuser . ':' . Config::$dbpass . '@' . Config::$dbhost . '/' . Config::$dbname . $dsnOptions;
$db = \ADONewConnection($dsn);
if (!$db) {
  $message = 'DB connection failed, please check your config settings.';
  echo $template->render(['message' => $message]);
  exit;
}

switch ($step) {
  case 1:
    $file = __DIR__ . '/dbBase.yaml';

    $dbBase = file_get_contents($file);
    $definition = \Spyc::YAMLLoadString($dbBase);

    foreach ($definition as $table => $tableData) {
      $sqlPrimary = '';
      $sqlColumns = [];
      foreach ($tableData['columns'] as $column => $columnData) {
        $sqlColumn = "`$column` ";
        $sqlColumn .= ' ' . $columnData['type'];
        $sqlColumn .= isset($columnData['notnull']) && $columnData['notnull'] ? ' NOT NULL' : '';
        $sqlColumn .= (isset($columnData['autoincrement']) ? ' AUTO_INCREMENT' : '');
        $sqlColumn .= " COMMENT '" . $columnData['comment'] . "'";
        $sqlColumns[] = $sqlColumn;

        if (isset($columnData['primary'])) {
          $sqlPrimary = 'ALTER TABLE `$column` ADD PRIMARY KEY (`$column`);';
        }
      }
      $sqlCreate = " CREATE TABLE IF NOT EXISTS `$table` (" . implode(', ', $sqlColumns) . ');';
      $db->execute($sqlCreate);
      $db->execute($sqlPrimary);
    }
}
