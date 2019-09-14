#!/usr/bin/php
<?php

global $argv;
include_once(pathinfo(__FILE__, PATHINFO_DIRNAME) . '/../../config.php');
include_once(Config::$dirIncludes . 'cli/class.CLI_images2video.php');

$images2video = new CLI_images2video();
$images2video->exec($argv);

exit;
