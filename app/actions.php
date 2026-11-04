<?php
require_once __DIR__.'/vehicles.php';
function handle_booking_submit():never {
 noindex();require_csrf();
 $period=rental_window($_POST);$pick=input('pickup');$drop=input('dropoff');$id=(int)($_POST['vehicle_id']??0);
 $name=input('name',100);$phone=preg_replace('/\D/','',input('phone',20));$email=input('email',200);
 if(!$period||!valid_location($pick)||!valid_location($drop)||$id<1||mb_strlen($name)<2||!preg_match('/^[6-9]\d{9}$/',$phone)||($email!==''&&!filter_var($email,FILTER_VALIDATE_EMAIL))){http_response_code(422);exit('Please return to the booking form and enter complete, valid details.');}
 $vehicle=vehicle_by_id($id);if(!$vehicle){http_response_code(409);exit('This vehicle is not currently approved for bookings.');}
 try {$estimate=rental_estimate($vehicle,...$period);}catch(Throwable $ex){http_response_code(422);exit(e($ex->getMessage()));}
 $pdo=try_db();if(!$pdo){http_response_code(503);exit('Booking database not configured. Please contact us on WhatsApp: +91 9181628882');}
 $ref='SF'.date('ymd').strtoupper(substr(bin2hex(random_bytes(5)),0,9));
 $secret=bin2hex(random_bytes(24));
 try {
  $pdo->beginTransaction();
  $s=$pdo->prepare("SELECT id FROM vehicles WHERE id=? AND approved=1 AND status='active' FOR UPDATE");$s->execute([$id]);
  if(!$s->fetch()||!availability_check($pdo,$id,$period[0]->format('Y-m-d H:i:s'),$period[1]->format('Y-m-d H:i:s'))){$pdo->rollBack();http_response_code(409);exit('This vehicle is no longer eligible for the selected dates.');}
  $s=$pdo->prepare("INSERT INTO bookings(reference,access_hash,vehicle_id,customer_name,customer_phone,customer_email,pickup_location,return_location,pickup_at,return_at,estimated_total,estimated_days,status,payment_status,created_at) VALUES(?,?,?,?,?,?,?,?,?,?,?,?, 'pending','unpaid',NOW())");
  $s->execute([$ref,hash('sha256',$secret),$id,$name,$phone,$email,$pick,$drop,$period[0]->format('Y-m-d H:i:s'),$period[1]->format('Y-m-d H:i:s'),$estimate['subtotal'],$estimate['days']]);
  $pdo->prepare("INSERT INTO booking_events(booking_id,event,created_at) VALUES(?, 'request_submitted',NOW())")->execute([(int)$pdo->lastInsertId()]);
  $pdo->commit();
 }catch(Throwable $ex){if($pdo->inTransaction())$pdo->rollBack();error_log('SAAFAAR booking: '.$ex->getMessage());http_response_code(503);exit('Could not save your request. Please try again or contact WhatsApp support.');}
 $_SESSION['last_booking']=['ref'=>$ref,'secret'=>$secret,'name'=>$name];
 redirect('/booking-confirmation/?ref='.rawurlencode($ref));
}
function booking_for_customer(string $ref,string $access):?array {
 if(!preg_match('/^SF[A-Z0-9]{10,20}$/',$ref)||strlen($access)<32)return null;
 $p=try_db();if(!$p)return null;
 $s=$p->prepare("SELECT b.*,v.make,v.model,v.variant FROM bookings b JOIN vehicles v ON v.id=b.vehicle_id WHERE b.reference=? LIMIT 1");
 $s->execute([$ref]);$b=$s->fetch();
 return $b&&hash_equals($b['access_hash'],hash('sha256',$access))?$b:null;
}
function handle_utr():never {
 noindex();require_csrf();
 $ref=input('reference',32);$secret=input('access',90);$utr=input('utr',80);
 if(!preg_match('/^[A-Za-z0-9-]{8,40}$/',$utr)){http_response_code(422);exit('Invalid transaction reference.');}
 $b=booking_for_customer($ref,$secret);if(!$b){http_response_code(403);exit('Invalid booking access.');}
 $pdo=db();$s=$pdo->prepare("UPDATE bookings SET submitted_utr=?, payment_status='pending_verification' WHERE id=? AND payment_status NOT IN ('verified')");$s->execute([$utr,$b['id']]);
 $_SESSION['last_booking']=['ref'=>$ref,'secret'=>$secret,'name'=>$b['customer_name']];
 redirect('/booking-confirmation/?ref='.rawurlencode($ref));
}
function handle_enquiry():never {
 noindex();require_csrf();
 $name=input('name',100);$phone=preg_replace('/\D/','',input('phone',20));$message=input('message',2000);
 if(mb_strlen($name)<2||!preg_match('/^[6-9]\d{9}$/',$phone)||mb_strlen($message)<10){http_response_code(422);exit('Please enter a name, valid Indian mobile number and a message (at least 10 characters).');}
 $pdo=try_db();
 if($pdo){try{
   $s=$pdo->prepare("INSERT INTO enquiries(name,phone,message,created_at) VALUES(?,?,?,NOW())");$s->execute([$name,$phone,$message]);
   $_SESSION['enquiry_saved']=true;
 }catch(Throwable $ex){error_log('SAAFAAR enquiry: '.$ex->getMessage());}}
 $_SESSION['enquiry_message']=whatsapp("Hello SAAFAAR HOLIDAYS, I am {$name} (+91{$phone}). My enquiry: {$message}");
 redirect('/enquire/?submitted=1');
}
