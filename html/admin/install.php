<?php

require_once dirname(__DIR__) . '/../vendor/autoload.php';
use Datagator\Config;

Config::load();

$step = isset($_POST['step']) ? $_POST['step'] : 0;
$confirm = isset($_POST['confirm']) ? $_POST['confirm'] : '';

$dsnOptions = '';
if (sizeof(Config::$dboptions) > 0) {
  foreach (Config::$dboptions as $k => $v) {
    $dsnOptions .= sizeof($dsnOptions) == 0 ? '?' : '&';
    $dsnOptions .= "$k=$v";
  }
}
$dsnOptions = sizeof(Config::$dboptions) > 0 ? '?'.implode('&', Config::$dboptions) : '';
$dsn = Config::$dbdriver . '://' . Config::$dbuser . ':' . Config::$dbpass . '@' . Config::$dbhost . '/' . Config::$dbname . $dsnOptions;
$db = \ADONewConnection($dsn);

$loader = new Twig_Loader_Filesystem(Config::$adminTemplates);
//$twig = new Twig_Environment($loader, array(
//  'cache' => Config::$twigCache,
//));
$twig = new Twig_Environment($loader);

$menu = ['Login' => '/admin/login.php'];

if (!$db) {
  $message = [
    'type' => 'error',
    'text' => 'DB connection failed, please check your config settings.'
  ];
  echo $template->render(['message' => $message, 'menu' => $menu]);
  exit;
}

switch ($step) {
  case 0:
    $template = $twig->load('install_0.html');
    $message['text'] = "WARNING!";
    $message['type'] = 'error';
    echo $template->render(['message' => $message, 'menu' => $menu]);
    exit;
  case 1:
    $yaml = file_get_contents(Config::$dbBase);
    $definition = \Spyc::YAMLLoadString($yaml);
    $message = [
      'type' => 'info',
      'text' => 'Creating database tables...<br />'
    ];

    foreach ($definition as $table => $tableData) {
      $sqlPrimary = '';
      $sqlColumns = [];
      foreach ($tableData['columns'] as $column => $columnData) {
        $sqlColumn = "`$column` ";
        if (!isset($columnData['type'])) {
          $template = $twig->load('install_0.html');
          $message['text'] .= "Create `$table` fail!<br />";
          $message['text'] .= "Type missing in the metadata.";
          $message['type'] = 'error';
          echo $template->render(['message' => $message, 'menu' => $menu]);
          exit;
        }
        $sqlColumn .= ' ' . $columnData['type'];
        $sqlColumn .= isset($columnData['notnull']) && $columnData['notnull'] ? ' NOT NULL' : '';
        $sqlColumn .= isset($columnData['default']) ? (' DEFAULT ' . $columnData['default']) : '';
        $sqlColumn .= isset($columnData['autoincrement']) ? ' AUTO_INCREMENT' : '';
        $sqlColumn .= isset($columnData['primary']) ? ' PRIMARY KEY' : '';
        $sqlColumn .= isset($columnData['comment']) ? (" COMMENT '" . $columnData['comment'] . "'") : '';
        $sqlColumns[] = $sqlColumn;
      }
      $sqlCreate = "CREATE TABLE IF NOT EXISTS `$table` (" . implode(', ', $sqlColumns) . ');';
      if (empty($db->execute($sqlCreate))) {
        $template = $twig->load('install_0.html');
        $message['text'] .= "Create `$table` fail!<br />";
        $message['text'] .= "Processing halted. Please check the logs and retry.";
        $message['type'] = 'error';
        echo $template->render(['message' => $message, 'menu' => $menu]);
        exit;
      } else {
        $message['text'] .= "Create `$table` success!<br />";
      }
      $sqlTruncate = "TRUNCATE `$table`;";
      $db->execute($sqlTruncate);
      if (isset($tableData['data'])) {
        foreach ($tableData['data'] as $row) {
          $keys = [];
          $values = [];
          foreach ($row as $key => $value) {
            $keys[] = $key;
            $values[] = is_string($value) ? "\"$value\"" : $value;
          }
          $sqlRow = "INSERT INTO `$table` (" . implode(', ', $keys) . ') VALUES (' . implode(', ', $values) . ');';
          if (empty($db->execute($sqlRow))) {
            $template = $twig->load('install_0.html');
            $message['text'] .= "Populate `$table` fail!<br />";
            $message['text'] .= "Processing halted. Please check the logs and retry.";
            $message['type'] = 'error';
            echo $template->render(['message' => $message, 'menu' => $menu]);
            exit;
          }
        }
        $message['text'] .= "Populate `$table` success!<br />";
      }
    }
    $template = $twig->load('install_1.html');
    $message['text'] .= "Database Successfully created!";
    echo $template->render(['message' => $message, 'menu' => $menu]);
    exit;
    break;
}
