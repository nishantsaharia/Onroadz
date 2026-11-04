<?php
declare(strict_types=1);
date_default_timezone_set('Asia/Kolkata');
define('ROOT', dirname(__DIR__));
if (is_file(ROOT.'/.env')) {
  foreach (file(ROOT.'/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line=trim($line);
    if ($line==='' || $line[0]==='#' || !str_contains($line,'=')) continue;
    [$key,$value]=explode('=',$line,2);
    $key=trim($key); $value=trim($value," \t\n\r\0\x0B\"'");
    if (preg_match('/^[A-Z][A-Z0-9_]*$/',$key) && getenv($key)===false) putenv($key.'='.$value);
  }
}
if (session_status()!==PHP_SESSION_ACTIVE) {
  session_name('SAAFAARSESSID');
  session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off','httponly'=>true,'samesite'=>'Lax']);
  ini_set('session.use_strict_mode','1'); session_start();
}
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
function setting(string $key,string $default=''):string {return (string)(getenv($key)?:$default);}
function e(?string $s):string {return htmlspecialchars((string)$s,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function site_url(string $path=''):string {return 'https://saafaar.in/'.ltrim($path,'/');}
function uri_path():string {
  $p=(string)(parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH)?:'/');
  return '/'.trim(rawurldecode($p),'/').($p==='/'?'':'/');
}
function csrf_token():string {return $_SESSION['csrf']??=bin2hex(random_bytes(32));}
function csrf_ok():bool{return isset($_POST['_csrf'])&&hash_equals(csrf_token(),(string)$_POST['_csrf']);}
function require_csrf():void {if(!csrf_ok()){http_response_code(419);exit('Your session expired. Please go back and try again.');}}
function redirect(string $path):never {header('Location: '.($path[0]==='/'?$path:('/'.$path)),true,303);exit;}
function db():PDO {
 static $pdo=null;
 if($pdo instanceof PDO)return $pdo;
 $host=setting('DB_HOST');$name=setting('DB_NAME');$user=setting('DB_USER');
 if($host===''||$name===''||$user==='')throw new RuntimeException('Database not configured. Follow docs/INSTALLATION.md.');
 $pdo=new PDO("mysql:host={$host};dbname={$name};charset=utf8mb4",$user,setting('DB_PASS'),[
 PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
 PDO::ATTR_EMULATE_PREPARES=>false,PDO::ATTR_TIMEOUT=>5]);
 return $pdo;
}
function try_db():?PDO {try{return db();}catch(Throwable $e){error_log('SAAFAAR DB: '.$e->getMessage());return null;}}
function is_post():bool{return strtoupper($_SERVER['REQUEST_METHOD']??'GET')==='POST';}
function input(string $name,int $max=250):string {return mb_substr(trim((string)($_POST[$name]??'')),0,$max);}
function valid_dt(string $date,string $time):?DateTimeImmutable {
  if(!preg_match('/^\d{4}-\d{2}-\d{2}$/',$date)||!preg_match('/^\d{2}:\d{2}$/',$time))return null;
  $dt=DateTimeImmutable::createFromFormat('!Y-m-d H:i',$date.' '.$time,new DateTimeZone('Asia/Kolkata'));
  return $dt && $dt->format('Y-m-d H:i')===$date.' '.$time?$dt:null;
}
function rental_window(array $a):?array {
  $start=valid_dt((string)($a['start_date']??''),(string)($a['start_time']??''));
  $end=valid_dt((string)($a['end_date']??''),(string)($a['end_time']??''));
  if(!$start||!$end||$start<=new DateTimeImmutable('now',new DateTimeZone('Asia/Kolkata'))||$end<=$start||$end->getTimestamp()-$start->getTimestamp()>90*86400)return null;
  return [$start,$end];
}
function locations():array {return ['Guwahati Airport','Guwahati Railway Station','Kamakhya Railway Station','Guwahati City'];}
function valid_location(string $loc):bool{return in_array($loc,locations(),true);}
function whatsapp(string $message):string {return 'https://wa.me/919181628882?text='.rawurlencode($message);}
function fmt_money($amount):string{return $amount===null?'On request':'₹'.number_format((float)$amount,0);}
function json_ld(array $data):string{return json_encode($data,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOTIONS)?:'{}';}
function noindex():void {header('X-Robots-Tag: noindex, nofollow');header('Cache-Control: private, no-store');}
