<?php
declare(strict_types=1);
require __DIR__.'/../app/vehicles.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex');
$cars=array_map(static fn($v)=>['slug'=>$v['slug'],'make'=>$v['make'],'model'=>$v['model'],'category'=>$v['category'],'transmission'=>$v['transmission'],'seats'=>$v['seats'],'daily_rate'=>$v['daily_rate']===null?null:(float)$v['daily_rate']],approved_vehicles());
echo json_encode(['vehicles'=>$cars],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
