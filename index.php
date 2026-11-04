<?php
declare(strict_types=1);
require_once __DIR__.'/app/views.php';
require_once __DIR__.'/app/actions.php';
$path=uri_path();
if($path==='/request-booking/'&&is_post())handle_booking_submit();
if($path==='/enquire/'&&is_post())handle_enquiry();
if($path==='/admin/') {require ROOT.'/admin/index.php';exit;}
if($path==='/') {home_page();exit;}
if($path==='/cars/') {cars_page();exit;}
if($path==='/travel-guides/') {guide_index_page();exit;}
if($path==='/search/') {search_page();exit;}
if($path==='/request-booking/') {request_booking_page();exit;}
if($path==='/booking-confirmation/') {booking_confirmation_page();exit;}
if($path==='/manage-booking/') {manage_booking_page();exit;}
if($path==='/contact/') {contact_page();exit;}
if($path==='/enquire/') {contact_page(true);exit;}
if($path==='/offline/') {offline_page();exit;}
if($path==='/404/') {not_found_page();exit;}
if(preg_match('#^/cars/([a-z0-9-]+)/$#',$path,$m)) {vehicle_detail_page($m[1]);exit;}
if(preg_match('#^/(?:blog|travel-guides)/([a-z0-9-]+)/$#',$path,$m)) {
 if(str_starts_with($path,'/blog/'))redirect('/travel-guides/'.$m[1].'/');
 editorial_page($m[1]);exit;
}
if($path==='/blog/')redirect('/travel-guides/');
$slug=trim($path,'/');
if(isset(public_pages()[$slug])){info_page($slug);exit;}
not_found_page();
