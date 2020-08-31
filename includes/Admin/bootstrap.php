<?php
/**
 * Bootstrap file for GaterData admin.
 *
 * @package Gaterdata
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0-or-later
 * @author john89
 * @copyright 2020-2030 GaterData
 * @link https://gaterdata.com
 */

?>
<?php

require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';

use Gaterdata\Core\Config;

session_start();

// Get the settings.
$config = new Config();
$settings = $config->all();
// Move slim config to the root of the settings array.
foreach ($settings['admin']['slim'] as $key => $value) {
    $settings[$key] = $value;
}

// Instantiate the app
$app = new \Slim\App(['settings' => $settings]);

// Set up dependencies.
require  __DIR__ . '/container.php';

// Register routes.
require __DIR__ . '/routes.php';

return $app;
