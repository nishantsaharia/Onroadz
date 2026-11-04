<?php
declare(strict_types=1);
require __DIR__.'/../app/core.php';
if(PHP_SAPI!=='cli'){http_response_code(403);exit('Use cPanel Terminal / SSH only.');}
if($argc!==2){fwrite(STDERR,"Usage: php bin/create-admin.php username\n");exit(1);}
$username=$argv[1];
if(!preg_match('/^[a-zA-Z0-9_.-]{4,40}$/',$username))exit("Invalid username\n");
echo "Enter a strong password: ";system('stty -echo');$password=trim((string)fgets(STDIN));system('stty echo');echo "\n";
if(strlen($password)<14)exit("Use 14+ characters\n");
$s=db()->prepare('INSERT INTO admins(username,password_hash) VALUES(?,?)');
$s->execute([$username,password_hash($password,PASSWORD_DEFAULT)]);echo "Administrator created.\n";
