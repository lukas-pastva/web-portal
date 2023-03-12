<?php

session_cache_expire(60);
session_start();
ob_start();
ini_set('arg_separator.output', '&amp;');
error_reporting(E_ALL & ~E_NOTICE);
date_default_timezone_set('UTC');
mb_internal_encoding('UTF-8');

$connId = mysqli_connect($_ENV['MYSQL_HOST'], $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD'], $_ENV['MYSQL_DATABASE']);

const UPLOADS = "/var/www/uploads";

$rootDir = opendir("/var/www/html");
while (($file = readdir($rootDir)) !== false) {
    if (is_dir($file) && substr($file, 0, 4) == 'inc.') {

        $subDir = opendir("/var/www/html/" . $file);
        while (($phpFile = readdir($subDir)) !== false) {
            if (!is_dir($phpFile) && substr($phpFile, -7) == 'inc.php') {
                include_once('./' . $file . '/' . $phpFile);
            }
        }
        closedir($subDir);
    }
}
closedir($rootDir);

$asynchCallEnum = array();
$asynchCallEnum['cam-ml'] = 1;

smart_mysql_query('SET NAMES utf8');