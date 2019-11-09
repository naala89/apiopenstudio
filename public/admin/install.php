<?php

use Gaterdata\Admin;
use Gaterdata\Db;
use Gaterdata\Core\ApiException;
use Gaterdata\Core\Config;

require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';

$settings = new Config();

// Get the user's origin and next step.
$from = isset($_POST['from_step']) ? intval($_POST['from_step']) : 0;
$step = isset($_POST['next_step']) ? intval($_POST['next_step']) : 0;

// User will start not logged in.
$menu = ['Login' => '/'];

// Twig definition.
$loader = new Twig_Loader_Filesystem($settings->__get(['api', 'base_path']) . $settings->__get(['twig', 'template_path']));
$twig = new Twig_Environment($loader, $settings->__get(['twig', 'options']));
if ($settings->__get(['twig', 'options', 'debug'])) {
  $twig->addExtension(new \Twig\Extension\DebugExtension());
}

// DB link.
$dsnOptionsArr = [];
foreach ($settings->__get(['db', 'options']) as $k => $v) {
  $dsnOptionsArr[] = "$k=$v";
}
$dsnOptions = count($dsnOptionsArr) > 0 ? ('?' . implode('&', $dsnOptionsArr)) : '';
$dsn = $settings->__get(['db', 'driver']) . '://root:'
  . $settings->__get(['db', 'root_password']) . '@'
  . $settings->__get(['db', 'host']) . '/'
  . $settings->__get(['db', 'database']) . $dsnOptions;

if (!$db = \ADONewConnection($dsn)) {
  $messages[] = [
    'type' => 'error',
    'text' => 'DB connection failed, please check your config settings.',
  ];
  $template = $twig->load("install/install_$from.twig");
  echo $template->render(['messages' => $messages, 'menu' => $menu]);
  exit;
}

switch ($step) {
  case 0:
    // Check user wants to continue.
    $template = $twig->load("install/install_$from.twig");
    $messages['error'][] = 'Continuing will create a new database and erase the current database, if it exists.<br />';
    echo $template->render(['messages' => $messages, 'menu' => $menu]);
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
    $yaml = file_get_contents($settings->__get(['api', 'base_path']) . $settings->__get(['db', 'definition_path']));
    $definition = \Spyc::YAMLLoadString($yaml);
    $template = $twig->load('install/install_1.twig');
    $messages['info'][] = 'Creating database tables...';

    // Create the database, user and permissions.
    $sql = 'CREATE DATABASE ' . $settings->__get(['db', 'database']) . 'IF NOT EXISTS';
    $db->execute($sql);
    $messages['info'][] = 'CREATE DATABASE success!';
    $sql = 'CREATE USER IF NOT EXISTS "' . $settings->__get(['db', 'username']) . '"@"'  . $settings->__get(['db', 'host']) . '" IDENTIFIED BY "' . $settings->__get(['db', 'password']) . '"';
    $db->execute($sql);
    $messages['info'][] = 'CREATE USER success!';
    $sql = 'GRANT ALL PRIVILEGES ON * . * TO "' . $settings->__get(['db', 'username']) . '"@"'  . $settings->__get(['db', 'host']) . '"';
    $db->execute($sql);
    $sql = 'FLUSH PRIVILEGES';
    $db->execute($sql);
    $messages['info'][] = 'GRANT PRIVILEGES success!';

    // Parse the DB  table definition array.
    foreach ($definition as $table => $tableData) {
      $sqlPrimary = '';
      $sqlColumns = [];
      foreach ($tableData['columns'] as $column => $columnData) {
        // Column definitions.
        $sqlColumn = "`$column` ";
        if (!isset($columnData['type'])) {
          $messages['error'][] = "CREATE TABLE `$table` fail!";
          $messages['error'][] = 'Type missing in the metadata.';
          echo $template->render(['messages' => $messages, 'menu' => $menu]);
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
        $messages['error'][] = "CREATE TABLE `$table` fail!";
        $messages['error'][] = 'Processing halted. Please check the logs and retry';
        echo $template->render(['messages' => $messages, 'menu' => $menu]);
        exit;
      } else {
        $messages['info'][] = "CREATE TABLE `$table` success!";
      }
      // Add data if required.
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
            $messages['error'][] = "INSERT into `$table` fail!";
            $messages['error'][] = 'Processing halted. Please check the logs and retry';
            echo $template->render(['messages' => $messages, 'menu' => $menu]);
            exit;
          }
        }
        $messages['info'][] = "INSERT into `$table` success!";
      }
    }

    $messages['info'][] = 'Database Successfully created!<br />';
    echo $template->render(['messages' => $messages, 'menu' => $menu]);
    exit;
    break;

  case 2:
    // Create user.
    if ($from == 2) {
      // This is a post from the user create form.
      $username = !empty($_POST['username']) ? $_POST['username'] : NULL;
      $password = !empty($_POST['password']) ? $_POST['password'] : NULL;
      $email = !empty($_POST['email']) ? $_POST['email'] : NULL;
      $honorific = !empty($_POST['honorific']) ? $_POST['honorific'] : NULL;
      $nameFirst = !empty($_POST['name_first']) ? $_POST['name_first'] : NULL;
      $nameLast = !empty($_POST['name_last']) ? $_POST['name_last'] : NULL;
      $company = !empty($_POST['company']) ? $_POST['company'] : NULL;
      $website = !empty($_POST['website']) ? $_POST['website'] : NULL;
      $addressStreet = !empty($_POST['address_street']) ? $_POST['address_street'] : NULL;
      $addressSuburb = !empty($_POST['address_suburb']) ? $_POST['address_suburb'] : NULL;
      $addressCity = !empty($_POST['address_city']) ? $_POST['address_city'] : NULL;
      $addressState = !empty($_POST['address_state']) ? $_POST['address_state'] : NULL;
      $addressCountry = !empty($_POST['address_country']) ? $_POST['address_country'] : NULL;
      $addressPostcode = !empty($_POST['address_postcode']) ? $_POST['address_postcode'] : NULL;
      $phoneMobile = !empty($_POST['phone_mobile']) ? $_POST['phone_mobile'] : 0;
      $phoneWork = !empty($_POST['phone_work']) ? $_POST['phone_work'] : 0;
      if (empty($username) ||
        empty($password) ||
        empty($email)) {
        // Missing mandatory fields.
        $messages['error'][] = 'Required fields not entered.';
        $template = $twig->load('install/install_2.twig');
        echo $template->render(['messages' => $messages, 'menu' => $menu]);
        exit;
      }
      // Create the helper classes.
      $userMapper = new Db\UserMapper($db);
      $user = $userMapper->findByUsername($username);
      if (!empty($user->getUid())) {
        // User already created, user must have revisited the page.
        $messages['error'][] = 'User already exists, please restart the installation process.';
        $template = $twig->load('install/install_2.twig');
        echo $template->render(['messages' => $messages, 'menu' => $menu]);
        exit;
      }
      try {
        $user = new Db\User(
          NULL,
          1,
          $username,
          NULL,
          NULL,
          NULL,
          $email,
          $honorific,
          $nameFirst,
          $nameLast,
          $company,
          $website,
          $addressStreet,
          $addressSuburb,
          $addressCity,
          $addressState,
          $addressCountry,
          $addressPostcode,
          $phoneMobile,
          $phoneWork
        );
        $user->setPassword($password);
        $userMapper->save($user);

      } catch (ApiException $e) {
        $template = $twig->load("install/install_$from.twig");
        $messages['error'][] = 'An error occurred creating your user: ' . $e->getMessage();
        echo $template->render([
          'messages' => $messages,
          'menu' => $menu,
        ]);
        exit;
      }

      // Assign owner role.
      try {
        $user = $userMapper->findByUsername($username);
        $uid = $user->getUid();
        if (empty($uid)) {
          throw new ApiException('Could not find the newly created user.');
        }
        $roleMapper = new Db\RoleMapper($db);
        $role = $roleMapper->findByName('Administrator');
        $rid = $role->getRid();
        $userRole = new Db\UserRole(
          NULL,
          NULL,
          NULL,
          $uid,
          $rid
        );
        $userRoleMapper = new Db\UserRoleMapper($db);
        $userRoleMapper->save($userRole);
      } catch (ApiException $e) {
        $template = $twig->load("install/install_$from.twig");
        $messages['error'][] = 'An error occurred creating your Owner role: ' . $e->getMessage();
        echo $template->render([
          'messages' => $messages,
          'menu' => $menu,
        ]);
        exit;
      }

      // User created, continue to next page.
      $template = $twig->load('install/install_3.twig');
      echo $template->render(['menu' => $menu, 'username' => $username]);
      exit;
    }

    // Fallback to rendering the create user form (user is from previous page).
    $template = $twig->load('install/install_2.twig');
    echo $template->render(['menu' => $menu]);
    exit;
    break;
}
