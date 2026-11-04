<?php
require_once __DIR__.'/vehicles.php';
function admin_logged_in():bool{return !empty($_SESSION['admin_id'])&&is_numeric($_SESSION['admin_id']);}
function admin_guard():void {if(!admin_logged_in()){http_response_code(403);exit('Sign in required.');}}
function admin_login():never {
 require_csrf();$pdo=try_db();if(!$pdo){http_response_code(503);exit('Database not configured.');}
 $blockedUntil=(int)($_SESSION['login_blocked_until']??0);
 if($blockedUntil>time()){http_response_code(429);exit('Too many attempts; try again later.');}
 $name=input('username',120);$pass=(string)($_POST['password']??'');
 $s=$pdo->prepare('SELECT id,password_hash FROM admins WHERE username=? AND active=1 LIMIT 1');$s->execute([$name]);$a=$s->fetch();
 if(!$a||!password_verify($pass,$a['password_hash'])){
  $_SESSION['login_failures']=(int)($_SESSION['login_failures']??0)+1;
  if($_SESSION['login_failures']>=5){$_SESSION['login_blocked_until']=time()+900;$_SESSION['login_failures']=0;}
  $_SESSION['login_error']='Incorrect credentials.';redirect('/admin/');
 }
 session_regenerate_id(true);$_SESSION['admin_id']=(int)$a['id'];$_SESSION['login_failures']=0;
 redirect('/admin/');
}
function admin_update_vehicle():never {
 admin_guard();require_csrf();$pdo=db();$id=(int)($_POST['id']??0);
 $fields=['make'=>input('make',80),'model'=>input('model',100),'variant'=>input('variant',100),'year'=>(int)($_POST['year']??0),
  'category'=>input('category',50),'transmission'=>input('transmission',20),'fuel'=>input('fuel',30),
  'seats'=>(int)($_POST['seats']??5),'daily_rate'=>input('daily_rate',20),'included_km'=>input('included_km',20),
  'min_days'=>(int)($_POST['min_days']??1),'slug'=>strtolower(input('slug',150)),'status'=>input('status',20),'approved'=>empty($_POST['approved'])?0:1];
 if(!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/',$fields['slug'])||mb_strlen($fields['model'])<2||!in_array($fields['status'],['draft','active','inactive'],true)||!in_array($fields['transmission'],['Manual','Automatic'],true)||!in_array($fields['category'],['Hatchback','Sedan','Compact SUV','SUV','Seven Seater'],true))exit('Invalid vehicle fields');
 $fields['daily_rate']=$fields['daily_rate']===''?null:(float)$fields['daily_rate'];
 $fields['included_km']=$fields['included_km']===''?null:(int)$fields['included_km'];
 if($fields['daily_rate']!==null&&$fields['daily_rate']<=0)exit('Invalid daily rate');
 $fields['year']=($fields['year']>=2000&&$fields['year']<=2035)?$fields['year']:null;
 if($id){
  $columns=array_keys($fields);
  $sql='UPDATE vehicles SET '.implode(',',array_map(fn($k)=>$k.'=?',$columns)).' WHERE id=?';
  $pdo->prepare($sql)->execute([...array_values($fields),$id]);
 } else {
  $columns=array_keys($fields);
  $sql='INSERT INTO vehicles ('.implode(',',$columns).') VALUES ('.implode(',',array_fill(0,count($columns),'?')).')';
  $pdo->prepare($sql)->execute(array_values($fields));
 }
 redirect('/admin/?tab=vehicles');
}
function admin_change_booking():never {
 admin_guard();require_csrf();$pdo=db();$id=(int)($_POST['id']??0);
 $status=input('status',30);$payment=input('payment_status',40);
 if(!in_array($status,['pending','confirmed','active','completed','cancelled','rejected'],true)||!in_array($payment,['unpaid','pending_verification','verified','rejected'],true))exit('Invalid status');
 try {
  $pdo->beginTransaction();
  $s=$pdo->prepare("SELECT * FROM bookings WHERE id=? FOR UPDATE");$s->execute([$id]);$b=$s->fetch();
  if(!$b){$pdo->rollBack();exit('Booking not found');}
  if(in_array($status,['confirmed','active'],true)){
   $v=$pdo->prepare('SELECT id FROM vehicles WHERE id=? FOR UPDATE');$v->execute([$b['vehicle_id']]);$v->fetch();
   $overlap=$pdo->prepare("SELECT COUNT(*) FROM bookings WHERE id<>? AND vehicle_id=? AND status IN ('confirmed','active') AND pickup_at<? AND return_at>?");
   $overlap->execute([$id,$b['vehicle_id'],$b['return_at'],$b['pickup_at']]);
   $block=$pdo->prepare("SELECT COUNT(*) FROM vehicle_blocks WHERE vehicle_id=? AND starts_at<? AND ends_at>?");
   $block->execute([$b['vehicle_id'],$b['return_at'],$b['pickup_at']]);
   if((int)$overlap->fetchColumn()>0||(int)$block->fetchColumn()>0){$pdo->rollBack();exit('Cannot confirm overlapping booking or blocked dates');}
  }
  $pdo->prepare('UPDATE bookings SET status=?,payment_status=? WHERE id=?')->execute([$status,$payment,$id]);
  $pdo->prepare("INSERT INTO booking_events(booking_id,event,actor_admin_id,created_at) VALUES(?,?,?,NOW())")->execute([$id,'status:'.$status.'/payment:'.$payment,(int)$_SESSION['admin_id']]);
  $pdo->commit();
 }catch(Throwable $ex){if($pdo->inTransaction())$pdo->rollBack();error_log('Booking admin '.$ex->getMessage());http_response_code(503);exit('Unable to update booking');}
 redirect('/admin/?tab=bookings');
}
function admin_upload_image():never {
 admin_guard();require_csrf();$id=(int)($_POST['vehicle_id']??0);$pdo=db();
 $s=$pdo->prepare('SELECT id,make,model FROM vehicles WHERE id=?');$s->execute([$id]);if(!$s->fetch())exit('Vehicle not found');
 if(!isset($_FILES['photo'])||$_FILES['photo']['error']!==UPLOAD_ERR_OK||$_FILES['photo']['size']>8*1024*1024)exit('Upload failed or file too large');
 $tmp=$_FILES['photo']['tmp_name'];$mime=(new finfo(FILEINFO_MIME_TYPE))->file($tmp);
 if(!in_array($mime,['image/jpeg','image/png','image/webp'],true)||!getimagesize($tmp))exit('Only valid PNG, JPG or WebP images are accepted');
 $ext=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$mime];
 $filename=bin2hex(random_bytes(16)).'.'.$ext;
 $dir=ROOT.'/assets/uploads';if(!is_dir($dir))mkdir($dir,0755,true);
 if(!move_uploaded_file($tmp,$dir.'/'.$filename))exit('Could not store photo.');
 @chmod($dir.'/'.$filename,0644);
 $s=$pdo->prepare('INSERT INTO vehicle_images(vehicle_id,file_path,alt_text,sort_order) VALUES(?,?,?,?)');
 $s->execute([$id,'/assets/uploads/'.$filename,input('alt_text',200),(int)($_POST['sort_order']??0)]);
 redirect('/admin/?tab=vehicles');
}
