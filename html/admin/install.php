<?php

require_once dirname(__DIR__) . '/../vendor/autoload.php';
$settings = require dirname(dirname(__DIR__)) . '/config/settings.php';

$from = isset($_POST['from_step']) ? $_POST['from_step'] : 0;
$step = isset($_POST['next_step']) ? $_POST['next_step'] : 0;

$dsnOptions = '';
if (sizeof($settings['db']['options']) > 0) {
  foreach ($settings['db']['options'] as $k => $v) {
    $dsnOptions .= sizeof($dsnOptions) == 0 ? '?' : '&';
    $dsnOptions .= "$k=$v";
  }
}
$dsnOptions = sizeof($settings['db']['options']) > 0 ? '?'.implode('&', $settings['db']['options']) : '';
$dsn = $settings['db']['driver'] . '://'
  . $settings['db']['username'] . ':'
  . $settings['db']['password'] . '@'
  . $settings['db']['host'] . '/'
  . $settings['db']['database'] . $dsnOptions;
$db = \ADONewConnection($dsn);

$loader = new Twig_Loader_Filesystem($settings['twig']['path'] . '/install');
$twig = new Twig_Environment($loader/*, array(
  'cache' => $settings['twig']['cache_path'],
)*/);

$menu = ['Login' => '/admin/'];

if (!$db) {
  $message = [
    'type' => 'error',
    'text' => 'DB connection failed, please check your config settings.'
  ];
  $template = $twig->load("install_$from.twig");
  echo $template->render(['message' => $message, 'menu' => $menu]);
  exit;
}

switch ($step) {
  case 0:
    $template = $twig->load('install_0.twig');
    $message['text'] = "Continuing will erase any existing data in the database.";
    $message['type'] = 'warning';
    echo $template->render(['message' => $message, 'menu' => $menu]);
    exit;
  case 1:
    $yaml = file_get_contents($settings['db']['base']);
    $definition = \Spyc::YAMLLoadString($yaml);
    $template = $twig->load('install_1.twig');
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
          $message['text'] = "Create `$table` fail!<br />";
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
        $message['text'] = "Create `$table` fail!<br />";
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
            $message['text'] = "Populate `$table` fail!<br />";
            $message['text'] .= "Processing halted. Please check the logs and retry.";
            $message['type'] = 'error';
            echo $template->render(['message' => $message, 'menu' => $menu]);
            exit;
          }
        }
        $message['text'] .= "Populate `$table` success!<br />";
      }
    }
    $message['text'] .= "Database Successfully created!";
    echo $template->render(['message' => $message, 'menu' => $menu]);
    exit;
    break;
  case 2:
    if ($from == 2) {
      if (!isset($_POST['username']) || !isset($_POST['password'])) {
        $message['text'] = "Required username and password not entered.";
        $message['type'] = 'error';
        $template = $twig->load('install_2.twig');
        echo $template->render(['message' => $message, 'menu' => $menu]);
        exit;
      }
      $user = new \Datagator\Admin\User($settings);
      $uid = $user->create(
        !empty($_POST['username']) ? $_POST['username'] : NULL,
        !empty($_POST['password']) ? $_POST['password'] : NULL,
        !empty($_POST['email']) ? $_POST['email'] : NULL,
        !empty($_POST['honorific']) ? $_POST['honorific'] : NULL,
        !empty($_POST['name_first']) ? $_POST['name_first'] : NULL,
        !empty($_POST['name_last']) ? $_POST['name_last'] : NULL,
        !empty($_POST['company']) ? $_POST['company'] : NULL,
        !empty($_POST['website']) ? $_POST['website'] : NULL,
        !empty($_POST['address_street']) ? $_POST['address_street'] : NULL,
        !empty($_POST['address_suburb']) ? $_POST['address_suburb'] : NULL,
        !empty($_POST['address_city']) ? $_POST['address_city'] : NULL,
        !empty($_POST['address_state']) ? $_POST['address_state'] : NULL,
        !empty($_POST['address_country']) ? $_POST['address_country'] : NULL,
        !empty($_POST['address_postcode']) ? $_POST['address_postcode'] : NULL,
        !empty($_POST['phone_mobile']) ? $_POST['phone_mobile'] : 0,
        !empty($_POST['phone_work']) ? $_POST['phone_work'] : 0
      );
      if (!$uid) {
        $template = $twig->load('install_2.twig');
        $message['text'] = "Failed to save your user to the DB. Please check the logs.";
        $message['type'] = 'error';
        echo $template->render(['message' => $message, 'menu' => $menu]);
        exit;
      }
      $template = $twig->load('install_3.twig');
      echo $template->render(['menu' => $menu, 'uid' => $uid, 'username' => $_POST['username']]);
      exit;
    }
    $template = $twig->load('install_2.twig');
    echo $template->render(['menu' => $menu]);
    exit;
    break;
  case 3:
    $uid = isset($_POST['uid']) ? $_POST['uid'] : '';
    $username = isset($_POST['username']) ? $_POST['username'] : '';
    if (empty($uid) || empty($username)) {
      $message['text'] = "Required user id & name not received.";
      $message['type'] = 'error';
      $template = $twig->load('install_3.twig');
      echo $template->render(['message' => $message, 'menu' => $menu]);
      exit;
    }
    if ($from == 3) {
      $accountName = isset($_POST['account_name']) ? $_POST['account_name'] : '';
      if (empty($accountName)) {
        $message['text'] = "Required Account name not entered.";
        $message['type'] = 'error';
        $template = $twig->load('install_3.twig');
        echo $template->render(['message' => $message, 'menu' => $menu]);
        exit;
      }
      $account = new \Datagator\Admin\Account($settings);
      $accId = $account->create($accountName);
      if (!$accId) {
        $message['text'] = "Failed to save your account to the DB. Please check the logs.";
        $message['type'] = 'error';
        $template = $twig->load('install_3.twig');
        echo $template->render(['message' => $message, 'menu' => $menu]);
        exit;
      }
      $userRole = new \Datagator\Admin\UserRole($settings);
      $result = $userRole->create($uid, 'Owner', NULL, $accId);
      if (!$result) {
        $message['text'] = "Failed to Create the owner role for your user in your account. Please check the logs.";
        $message['type'] = 'error';
        $template = $twig->load('install_3.twig');
        echo $template->render(['message' => $message, 'menu' => $menu]);
        exit;
      }
      $template = $twig->load('install_4.twig');
      echo $template->render(['menu' => $menu, 'account_name' => $accountName]);
      exit;
    }
    $template = $twig->load('install_3.twig');
    echo $template->render(['menu' => $menu]);
    exit;
}
