<?php

require_once dirname(__DIR__) . '/../vendor/autoload.php';

use Datagator\Admin\User;
use Datagator\Admin\Account;
use Cascade\Cascade;

$settings = require dirname(dirname(__DIR__)) . '/config/settings.php';

// Create logger
Cascade::fileConfig($settings['log']['settings']);

// Get the user's origin and next step.
$from = isset($_POST['from_step']) ? $_POST['from_step'] : 0;
$step = isset($_POST['next_step']) ? $_POST['next_step'] : 0;

// User will start not logged in.
$menu = ['Login' => '/'];

// Twig definition.
$loader = new Twig_Loader_Filesystem($settings['twig']['path']);
$twig = new Twig_Environment($loader/*, array(
  'cache' => $settings['twig']['cache_path'],
)*/);

// DB link.
$dsnOptionsArr = [];
foreach ($settings['db']['options'] as $k => $v) {
  $dsnOptionsArr[] = "$k=$v";
}
$dsnOptions = count($dsnOptionsArr) > 0 ? ('?' . implode('&', $dsnOptionsArr)) : '';
$dsn = $settings['db']['driver'] . '://'
  . $settings['db']['username'] . ':'
  . $settings['db']['password'] . '@'
  . $settings['db']['host'] . '/'
  . $settings['db']['database'] . $dsnOptions;
$db = ADONewConnection($dsn);

if (!$db) {
  $message = [
    'type' => 'error',
    'text' => 'DB connection failed, please check your config settings.'
  ];
  $template = $twig->load("install/install_$from.twig");
  echo $template->render(['message' => $message, 'menu' => $menu]);
  exit;
}

switch ($step) {
  case 0:
    // Check user wants to continue.
    $template = $twig->load('install/install_0.twig');
    $message = [
      'type' => 'warning',
      'text' => "Continuing will erase any existing data in the database."
    ];
    echo $template->render(['message' => $message, 'menu' => $menu]);
    exit;
  case 1:
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
    // Fetch the DB definition.
    $yaml = file_get_contents($settings['db']['base']);
    $definition = \Spyc::YAMLLoadString($yaml);
    $template = $twig->load('install/install_1.twig');
    $message = [
      'type' => 'info',
      'text' => 'Creating database tables...<br />'
    ];

    // Parse the DB definition array.
    foreach ($definition as $table => $tableData) {
      $sqlPrimary = '';
      $sqlColumns = [];
      foreach ($tableData['columns'] as $column => $columnData) {
        // Column definitions.
        $sqlColumn = "`$column` ";
        if (!isset($columnData['type'])) {
          $message['text'] = "CREATE TABLE `$table` fail!<br />";
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
        // Stop if table create fails.

        $logger->error($e->gettrace());
        $message['text'] = "CREATE TABLE `$table` fail!<br />";
        $message['text'] .= "Processing halted. Please check the logs and retry.";
        $message['type'] = 'error';
        echo $template->render(['message' => $message, 'menu' => $menu]);
        exit;
      } else {
        $message['text'] .= "CREATE TABLE `$table` success!<br />";
      }
      // Empty the table in case it already existed.
      $sqlTruncate = "TRUNCATE `$table`;";
      $db->execute($sqlTruncate);
      if (isset($tableData['data'])) {
        // Populate the table.
        foreach ($tableData['data'] as $row) {
          $keys = [];
          $values = [];
          foreach ($row as $key => $value) {
            $keys[] = $key;
            $values[] = is_string($value) ? "\"$value\"" : $value;
          }
          $sqlRow = "INSERT INTO `$table` (" . implode(', ', $keys) . ') VALUES (' . implode(', ', $values) . ');';
          if (empty($db->execute($sqlRow))) {
            $message['text'] = "INSERT into `$table` fail!<br />";
            $message['text'] .= "Processing halted. Please check the logs and retry.";
            $message['type'] = 'error';
            echo $template->render(['message' => $message, 'menu' => $menu]);
            exit;
          }
        }
        $message['text'] .= "INSERT into `$table` success!<br />";
      }
    }
    $message['text'] .= "Database Successfully created!";
    echo $template->render(['message' => $message, 'menu' => $menu]);
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
        echo $template->render(['message' => $message, 'menu' => $menu]);
        exit;
      }
      $user = new User($settings['db']);
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
        // Failed to create the user
        $template = $twig->load('install/install_2.twig');
        $message = [
          'type' => 'error',
          'text' => 'Failed to save your user to the DB. Please check the logs.'
        ];
        echo $template->render(['message' => $message, 'menu' => $menu]);
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
  case 3:
    // Create the account.

    // Data preserved from previous page that we need for user account.
    $uid = isset($_POST['uid']) ? $_POST['uid'] : '';
    if (empty($uid)) {
      // missing required user id from previous page.
      $message = [
        'type' => 'error',
        'text' => 'Missing required user id. Please restart the install process.'
      ];
      $template = $twig->load('install/install_3.twig');
      echo $template->render(['message' => $message, 'menu' => $menu]);
      exit;
    }

    if ($from == 3) {
      // This is a current page submission, so create the account.
      $accountName = isset($_POST['account_name']) ? $_POST['account_name'] : '';
      if (empty($accountName)) {
        // Missing required data.
        $message = [
          'type' => 'error',
          'text' => 'Required Account name not entered.'
        ];
        $template = $twig->load('install/install_3.twig');
        echo $template->render(['message' => $message, 'menu' => $menu, 'uid' => $uid]);
        exit;
      }

      // Create the account.
      $account = new Account($settings['db']);
      $newAccount = $account->create($accountName);
      if (!$newAccount) {
        $message = [
          'type' => 'error',
          'text' => 'Failed to save your account to the DB. Please check the logs.',
        ];
        $template = $twig->load('install/install_3.twig');
        echo $template->render(['message' => $message, 'menu' => $menu, 'uid' => $uid]);
        exit;
      }

      // Assign the user to the account.
      $user = new User($settings['db']);
      $result = $user->findByUserId($uid);
      if (!$result) {
        $message = [
          'type' => 'error',
          'text' => 'Failed to find your user in the db. Please check the logs.',
        ];
        $template = $twig->load('install/install_3.twig');
        echo $template->render(['message' => $message, 'menu' => $menu, 'uid' => $uid]);
        exit;
      }
      $result = $user->assignToAccountName($accountName);
      if (!$result) {
        $message = [
          'type' => 'error',
          'text' => 'Failed to find assign your user the the account. Please check the logs.',
        ];
        $template = $twig->load('install/install_3.twig');
        echo $template->render(['message' => $message, 'menu' => $menu, 'uid' => $uid]);
        exit;
      }

      // Create the user 'Owner' role for the user account.
      $result = $user->assignRole('Owner', $accountName);
      if (!$result) {
        $message = [
          'type' => 'error',
          'text' => 'Failed to Create the owner role for your user in your account. Please check the logs.'
        ];
        $template = $twig->load('install/install_3.twig');
        echo $template->render(['message' => $message, 'menu' => $menu, 'uid' => $uid]);
        exit;
      }

      // Success, render the success page.
      $template = $twig->load('install/install_4.twig');
      echo $template->render(['menu' => $menu, 'account_name' => $accountName]);
      exit;
    }

    // Fallback to render initial create account page (user arrives from previous page).
    $template = $twig->load('install/install_3.twig');
    echo $template->render(['menu' => $menu]);
    exit;
}
