<?php
declare(strict_types=1);
/** PHP built-in server development router; Apache uses .htaccess. */
$path=rawurldecode((string)(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH)?:'/'));
$file=__DIR__.$path;
if($path!=='/'&&is_file($file))return false;
if(is_dir($file)&&is_file(rtrim($file,'/').'/index.php')){require rtrim($file,'/').'/index.php';return true;}
require __DIR__.'/index.php';
