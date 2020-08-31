#!/usr/bin/php
<?php
/**
 * script to convert images to video.
 *
 * @package Gaterdata
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL-3.0-or-later
 * @author john89
 * @copyright 2020-2030 GaterData
 * @link https://gaterdata.com
 */

global $argv;
include_once(pathinfo(__FILE__, PATHINFO_DIRNAME) . '/../../config.php');
include_once(Config::$dirIncludes . 'cli/class.CLIImages2video.php');

$images2video = new CLI_images2video();
$images2video->exec($argv);

exit;
