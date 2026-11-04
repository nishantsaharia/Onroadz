<?php
declare(strict_types=1);
require_once __DIR__.'/../app/admin_core.php';
require_once __DIR__.'/../app/views.php';
noindex();
if(is_post()){
 $action=input('action',40);
 if($action==='login')admin_login();
 if($action==='logout'){require_csrf();unset($_SESSION['admin_id']);session_regenerate_id(true);redirect('/admin/');}
 if($action==='save_vehicle')admin_update_vehicle();
 if($action==='save_booking')admin_change_booking();
 if($action==='upload_photo')admin_upload_image();
 http_response_code(400);exit('Unsupported operation.');
}
head_html('SAAFAAR Admin Dashboard','Private SAAFAAR vehicle and booking administration.','/admin/','meghalaya-hills.webp',true);
site_header('');
$pdo=try_db();
if(!admin_logged_in()):
?>
<div class="admin-login"><div class="form-card narrow-form"><span class="eyebrow">SAAFAAR PRIVATE CONSOLE</span><h1>Welcome back.</h1><p>Sign in to manage your independent fleet and bookings.</p>
<?php if(!$pdo):?><p class="notice notice-error">Database is not configured. See <code>docs/INSTALLATION.md</code>.</p><?php endif;?>
<?php if(isset($_SESSION['login_error'])):?><p class="notice notice-error"><?=e($_SESSION['login_error'])?></p><?php unset($_SESSION['login_error']);endif;?>
<form method="post" class="stacked-form"><input type="hidden" name="_csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="login">
<label>Username<input name="username" autocomplete="username" required></label><label>Password<input type="password" name="password" autocomplete="current-password" required></label>
<button class="btn btn-dark btn-full" type="submit">Secure sign in <?=svg_icon('arrow')?></button></form></div></div>
<?php site_footer();exit; endif;
$tab=(string)($_GET['tab']??'dashboard');
$vehicles=[];$bookings=[];$enquiries=[];
if($pdo){
 try{
  $vehicles=$pdo->query("SELECT * FROM vehicles ORDER BY id DESC LIMIT 150")->fetchAll();
  $bookings=$pdo->query("SELECT b.*,v.make,v.model FROM bookings b JOIN vehicles v ON v.id=b.vehicle_id ORDER BY b.created_at DESC LIMIT 150")->fetchAll();
  $enquiries=$pdo->query("SELECT * FROM enquiries ORDER BY created_at DESC LIMIT 50")->fetchAll();
 }catch(Throwable $ex){error_log('SAAFAAR dashboard '.$ex->getMessage());}
}
?>
<div class="admin-wrap">
<div class="admin-title"><div><span class="eyebrow">OPERATIONS / SAAFAAR</span><h1>Business console</h1><p>Independent vehicle inventory and staff-reviewed bookings.</p></div><form method="post"><input type="hidden" name="_csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="logout"><button class="btn btn-outline" type="submit">Sign out</button></form></div>
<div class="admin-tabs"><a class="<?=$tab==='dashboard'?'active':''?>" href="/admin/?tab=dashboard">Overview</a><a class="<?=$tab==='vehicles'?'active':''?>" href="/admin/?tab=vehicles">Vehicles</a><a class="<?=$tab==='bookings'?'active':''?>" href="/admin/?tab=bookings">Bookings</a><a class="<?=$tab==='enquiries'?'active':''?>" href="/admin/?tab=enquiries">Enquiries</a></div>
<?php if($tab==='dashboard'):?>
<div class="admin-stats"><div><span>Approved vehicles</span><strong><?=count(array_filter($vehicles,fn($v)=>(int)$v['approved']===1&&$v['status']==='active'))?></strong></div><div><span>Pending bookings</span><strong><?=count(array_filter($bookings,fn($b)=>$b['status']==='pending'))?></strong></div><div><span>Confirmed bookings</span><strong><?=count(array_filter($bookings,fn($b)=>in_array($b['status'],['confirmed','active'],true)))?></strong></div><div><span>Enquiries shown</span><strong><?=count($enquiries)?></strong></div></div>
<div class="notice">Rental prices and availability are only published when a vehicle is marked active and approved. Payment references require manual staff verification.</div>
<?php elseif($tab==='vehicles'):?>
<section class="form-card"><h2>Add approved vehicle details</h2><p>Create as draft until its photography, variant, rate and availability have been checked.</p>
<form class="stacked-form" method="post"><input type="hidden" name="_csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="save_vehicle"><input type="hidden" name="id" value="0">
<div class="form-row"><label>Slug<input required name="slug" pattern="[a-z0-9]+(-[a-z0-9]+)*" placeholder="scorpio-n-automatic"></label><label>Make<input required name="make" placeholder="Mahindra"></label></div>
<div class="form-row"><label>Model<input required name="model" placeholder="Scorpio N"></label><label>Variant<input name="variant"></label></div>
<div class="form-row"><label>Year<input type="number" name="year" min="2000" max="2035"></label><label>Category<select name="category"><?php foreach(['Hatchback','Sedan','Compact SUV','SUV','Seven Seater'] as $c):?><option><?=e($c)?></option><?php endforeach;?></select></label></div>
<div class="form-row"><label>Transmission<select name="transmission"><option>Manual</option><option>Automatic</option></select></label><label>Fuel<input name="fuel" value="Petrol"></label></div>
<div class="form-row"><label>Seats<input type="number" name="seats" value="5" min="2" max="10"></label><label>Minimum days<input type="number" name="min_days" value="1" min="1" max="30"></label></div>
<div class="form-row"><label>Daily rate (₹, leave empty if unapproved)<input type="number" name="daily_rate" min="1" step="0.01"></label><label>Included kilometres (optional)<input type="number" name="included_km" min="0"></label></div>
<div class="form-row"><label>Status<select name="status"><option value="draft">Draft</option><option value="active">Active</option><option value="inactive">Inactive</option></select></label><label class="checkbox"><input type="checkbox" name="approved" value="1"> Approved for public listing</label></div>
<button class="btn btn-dark" type="submit">Save vehicle</button></form></section>
<h2>Vehicle records</h2>
<?php foreach($vehicles as $v):?><details class="admin-record"><summary><strong><?=e($v['make'].' '.$v['model'].' '.$v['variant'])?></strong><span><?=e($v['status'])?> · <?=((int)$v['approved']?'approved':'not approved')?></span></summary>
<form class="stacked-form" method="post"><input type="hidden" name="_csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="save_vehicle"><input type="hidden" name="id" value="<?=(int)$v['id']?>">
<div class="form-row"><label>Slug<input name="slug" required value="<?=e($v['slug'])?>"></label><label>Make<input name="make" required value="<?=e($v['make'])?>"></label></div>
<div class="form-row"><label>Model<input name="model" required value="<?=e($v['model'])?>"></label><label>Variant<input name="variant" value="<?=e($v['variant'])?>"></label></div>
<div class="form-row"><label>Year<input type="number" name="year" value="<?=e((string)$v['year'])?>"></label><label>Category<select name="category"><?php foreach(['Hatchback','Sedan','Compact SUV','SUV','Seven Seater'] as $c):?><option value="<?=e($c)?>" <?=$v['category']===$c?'selected':''?>><?=e($c)?></option><?php endforeach;?></select></label></div>
<div class="form-row"><label>Transmission<select name="transmission"><?php foreach(['Manual','Automatic'] as $x):?><option <?=$v['transmission']===$x?'selected':''?>><?=e($x)?></option><?php endforeach;?></select></label><label>Fuel<input name="fuel" value="<?=e($v['fuel'])?>"></label></div>
<div class="form-row"><label>Seats<input name="seats" type="number" value="<?=e((string)$v['seats'])?>"></label><label>Minimum days<input name="min_days" type="number" value="<?=e((string)$v['min_days'])?>"></label></div>
<div class="form-row"><label>Daily rate<input name="daily_rate" type="number" step="0.01" value="<?=e((string)$v['daily_rate'])?>"></label><label>Included kilometres<input name="included_km" type="number" value="<?=e((string)$v['included_km'])?>"></label></div>
<div class="form-row"><label>Status<select name="status"><?php foreach(['draft','active','inactive'] as $s):?><option <?=$v['status']===$s?'selected':''?>><?=e($s)?></option><?php endforeach;?></select></label><label class="checkbox"><input type="checkbox" name="approved" value="1" <?=(int)$v['approved']?'checked':''?>>Approved for public listing</label></div>
<button class="btn btn-dark">Update vehicle</button></form>
<form action="/admin/" method="post" enctype="multipart/form-data" class="stacked-form upload-row"><input type="hidden" name="_csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="upload_photo"><input type="hidden" name="vehicle_id" value="<?=(int)$v['id']?>">
<label>Add a real vehicle photo (JPG/PNG/WebP)<input type="file" name="photo" accept="image/jpeg,image/png,image/webp" required></label><label>Image description<input name="alt_text" maxlength="200"></label><label>Sort order<input type="number" name="sort_order" value="0"></label><button class="btn btn-outline">Upload image</button></form>
</details><?php endforeach;?>
<?php elseif($tab==='bookings'):?>
<h2>Booking requests and status review</h2><p class="fine-print">Set status to confirmed only after availability, terms and any required payment verification are satisfied. Conflicting confirmations are blocked transactionally.</p>
<?php foreach($bookings as $b):?><details class="admin-record"><summary><strong><?=e($b['reference'].' · '.$b['make'].' '.$b['model'])?></strong><span><?=e($b['status'])?> · <?=e($b['payment_status'])?></span></summary><div class="record-grid"><div><small>Name</small><strong><?=e($b['customer_name'])?></strong></div><div><small>Phone</small><strong><?=e($b['customer_phone'])?></strong></div><div><small>Pickup</small><strong><?=e($b['pickup_location'].' '.$b['pickup_at'])?></strong></div><div><small>Return</small><strong><?=e($b['return_location'].' '.$b['return_at'])?></strong></div><div><small>Estimate</small><strong><?=fmt_money($b['estimated_total'])?></strong></div><div><small>Submitted UTR</small><strong><?=e($b['submitted_utr']?:'None')?></strong></div></div>
<form method="post" class="form-row"><input type="hidden" name="_csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="action" value="save_booking"><input type="hidden" name="id" value="<?=(int)$b['id']?>">
<label>Booking status<select name="status"><?php foreach(['pending','confirmed','active','completed','cancelled','rejected'] as $s):?><option <?=$b['status']===$s?'selected':''?>><?=e($s)?></option><?php endforeach;?></select></label>
<label>Payment status<select name="payment_status"><?php foreach(['unpaid','pending_verification','verified','rejected'] as $s):?><option <?=$b['payment_status']===$s?'selected':''?> value="<?=e($s)?>"><?=e($s)?></option><?php endforeach;?></select></label>
<button class="btn btn-dark">Save review</button></form></details><?php endforeach;?>
<?php elseif($tab==='enquiries'):?>
<h2>Contact enquiries</h2><?php foreach($enquiries as $e):?><article class="admin-record"><h3><?=e($e['name'])?> · <?=e($e['phone'])?></h3><p><?=nl2br(e($e['message']))?></p><small><?=e($e['created_at'])?></small></article><?php endforeach;?>
<?php endif;?>
</div>
<?php site_footer();
