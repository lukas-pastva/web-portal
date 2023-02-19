<?php

session_cache_expire(60);
session_start();
ob_start();

try
{
    $connId = mysqli_connect($_ENV['MYSQL_HOST'], $_ENV['MYSQL_USER'], $_ENV['MYSQL_PASSWORD'], $_ENV['MYSQL_DATABASE']);
}
catch(Exception $e)
{
    echo $e->getMessage();
}

const PHOTO_CONFIRM = "100";
const ROOT_DIR = "/var/www/html";
const UPLOADS = "/var/www/uploads";

$rootDir = opendir(ROOT_DIR);
while (($file = readdir($rootDir)) !== false){
    if(is_dir($file) && substr($file,0, 4)=='inc.' ){

        $subDir = opendir(ROOT_DIR.'/'.$file);
        while (($phpFile = readdir($subDir)) !== false){
            if(!is_dir($phpFile) && substr($phpFile,-7)=='inc.php' ){
                include_once('./'.$file.'/'.$phpFile);
            }
        }
        closedir($subDir);
    }
}
closedir($rootDir);

$notListedFields = Array(
    'monthly',
    'length',
    'daily'
);

$asynchCallEnum = Array();
$asynchCallEnum['cam-ml'] = 1;

initial_functions();