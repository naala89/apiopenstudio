<?php

use Gaterdata\Admin;
use Cascade\Cascade;
use Gaterdata\Core\ApiException;

require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';

$settings = require dirname(dirname(__DIR__)) . '/config/settings.php';

// Create logger
Cascade::fileConfig($settings['log']['settings']);

// Get the user's origin and next step.
$from = isset($_POST['from_step']) ? intval($_POST['from_step']) : 0;
$step = isset($_POST['next_step']) ? intval($_POST['next_step']) : 0;

// User will start not logged in.
$menu = ['Login' => '/'];

// Twig definition.
$loader = new Twig_Loader_Filesystem($settings['twig']['path']);
$twig = new Twig_Environment($loader, $settings['twig']['options']);
if ($settings['twig']['options']['debug']) {
  $twig->addExtension(new \Twig\Extension\DebugExtension());
}

// Create the helper classes.
try {
  $user = new Admin\User($settings['db']);
} catch (ApiException $e) {
  $template = $twig->load("install/install_$from.twig");
  echo $template->render([
    'messages' => [
      [
        'type' => 'error',
        'text' => 'An error occurred: ' . $e->getMessage(),
      ]
    ],
    'menu' => $menu,
  ]);
  exit;
}

switch ($step) {
  case 0:
    // Check user wants to continue.
    $template = $twig->load("install/install_$from.twig");
    $messages[] = [
      'type' => 'error',
      'text' => 'Continuing will erase any existing data in the database.<br />',
    ];
    $message['text'] .= '<a href="login">Click here to abort and login</a>.';
    echo $template->render(['messages' => $messages, 'menu' => $menu]);
    exit;

  case 1:
    // DB link.
    $dsnOptionsArr = [];
    foreach ($settings['db']['options'] as $k => $v) {
      $dsnOptionsArr[] = "$k=$v";
    }
    $dsnOptions = count($dsnOptionsArr) > 0 ? ('?' . implode('&', $dsnOptionsArr)) : '';
    $dsn = $settings['db']['driver'] . '://root:'
      . $settings['db']['root_password'] . '@'
      . $settings['db']['host'] . '/'
      . $settings['db']['database'] . $dsnOptions;
    if (!$db = ADONewConnection($dsn)) {
      $messages[] = [
        'type' => 'error',
        'text' => 'DB connection failed, please check your config settings.',
      ];
      $template = $twig->load("install/install_$from.twig");
      echo $template->render(['messages' => $messages, 'menu' => $menu]);
      exit;
    }
    // Create and pre-populate the database.
    // If re-installation, remove any current logins.
    if (isset($_SESSION['accountName'])) {
      unset ($_SESSION['accountName']);
    }
    if (isset($_SESSION['accountId'])) {
      unset ($_SESSION['accountId']);
    }
    if (isset($_SESSION['token'])) {
      unset ($_SESSION['token']);
    }
    $yaml = file_get_contents($settings['db']['base']);
    $definition = \Spyc::YAMLLoadString($yaml);
    $template = $twig->load('install/install_1.twig');
    $message = [
      'type' => 'info',
      'text' => 'Creating database tables...<br />',
    ];

    // Create the database, user and permissions.
    $sql = 'CREATE DATABASE ' . $settings['db']['database'] . 'IF NOT EXISTS';
    $db->execute($sql);
    $message['text'] .= 'CREATE DATABASE success!<br />';
    $sql = 'CREATE USER IF NOT EXISTS "' . $settings['db']['username'] . '"@"'  . $settings['db']['host'] . '" IDENTIFIED BY "' . $settings['db']['password'] . '"';
    $db->execute($sql);
    $message['text'] .= 'CREATE USER success!<br />';
    $sql = 'GRANT ALL PRIVILEGES ON * . * TO "' . $settings['db']['username'] . '"@"'  . $settings['db']['host'] . '"';
    $db->execute($sql);
    $sql = 'FLUSH PRIVILEGES';
    $db->execute($sql);
    $message['text'] .= 'GRANT PRIVILEGES success!<br />';

    // Parse the DB  table definition array.
    foreach ($definition as $table => $tableData) {
      $sqlPrimary = '';
      $sqlColumns = [];
      foreach ($tableData['columns'] as $column => $columnData) {
        // Column definitions.
        $sqlColumn = "`$column` ";
        if (!isset($columnData['type'])) {
          $message['text'] .= "CREATE TABLE `$table` fail!<br />";
          $message['text'] .= "Type missing in the metadata.";
          $message['type'] = 'error';
          echo $template->render(['messages' => [$message], 'menu' => $menu]);
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
      $sqlDrop = "DROP TABLE IF EXISTS `$table`";
      $db->execute($sqlDrop);
      $sqlCreate = "CREATE TABLE IF NOT EXISTS `$table` (" . implode(', ', $sqlColumns) . ');';
      if (empty($db->execute($sqlCreate))) {
        // Stop if table create fails.

        $logger->error($e->gettrace());
        $message['text'] .= "CREATE TABLE `$table` fail!<br />";
        $message['text'] .= "Processing halted. Please check the logs and retry.";
        $message['type'] = 'error';
        echo $template->render(['messages' => [$message], 'menu' => $menu]);
        exit;
      } else {
        $message['text'] .= "CREATE TABLE `$table` success!<br/>";
      }
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
            $message['text'] .= "INSERT into `$table` fail!<br />";
            $message['text'] .= "Processing halted. Please check the logs and retry.";
            $message['type'] = 'error';
            echo $template->render(['messages' => [$message], 'menu' => $menu]);
            exit;
          }
        }
        $message['text'] .= "INSERT into `$table` success!<br/>";
      }
    }
    $message['text'] .= 'Database Successfully created!<br />';
    echo $template->render(['messages' => [$message], 'menu' => $menu]);
    exit;
    break;

  case 2:
    // Create user.
    if ($from == 2) {
      // This is a post from the user create form.
      if (empty($_POST['username']) ||
        empty($_POST['password']) ||
        empty($_POST['honorific']) ||
        empty($_POST['email']) ||
        empty($_POST['name_first']) ||
        empty($_POST['name_last'])) {
        // Missing mandatory fields.
        $message['text'] = "Required fields not entered.";
        $message['type'] = 'error';
        $template = $twig->load('install/install_2.twig');
        echo $template->render(['messages' => [$message], 'menu' => $menu]);
        exit;
      }
      $newUser = $user->create(
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
      if (!$newUser) {
        $template = $twig->load('install/install_2.twig');
        echo $template->render([
          'menu' => $menu,
          'messages' => [
            [
              'type' => 'error',
              'text' => 'Failed to save your user to the DB. Please check the logs.'
            ]
          ],
        ]);
        exit;
      }

      if (!$user->assignAdministrator()) {
        $template = $twig->load('install/install_2.twig');
        echo $template->render([
          'menu' => $menu,
          'messages' => [
            [
              'type' => 'error',
              'text' => 'Failed to assign your user administrator status. Please check the logs.'
            ]
          ],
        ]);
        exit;
      }

      // User created, continue to next page.
      $template = $twig->load('install/install_3.twig');
      echo $template->render(['menu' => $menu, 'uid' => $newUser['uid']]);
      exit;
    }

    // Fallback to rendering the create user form (user is from previous page).
    $template = $twig->load('install/install_2.twig');
    echo $template->render(['menu' => $menu]);
    exit;
    break;
}
