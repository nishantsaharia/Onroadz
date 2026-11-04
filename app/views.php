<?php
declare(strict_types=1);
require_once __DIR__.'/content.php';
require_once __DIR__.'/vehicles.php';
function head_html(string $title,string $desc,string $path='/',string $image='meghalaya-hills.webp',bool $private=false):void {
 if($private)noindex();
 $canonical=site_url(ltrim($path,'/'));
 $imageUrl=site_url('assets/img/'.basename($image));
 $organization=['@context'=>'https://schema.org','@type'=>'Organization','name'=>'SAAFAAR HOLIDAYS','url'=>'https://saafaar.in/','telephone'=>'+919181628882','logo'=>site_url('assets/img/saafaar-logo.webp'),'contactPoint'=>['@type'=>'ContactPoint','telephone'=>'+919181628882','contactType'=>'customer support']];
?><!doctype html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=e($title)?></title>
<meta name="description" content="<?=e($desc)?>">
<meta name="robots" content="<?=$private?'noindex,nofollow':'index,follow,max-image-preview:large'?>">
<link rel="canonical" href="<?=e($canonical)?>">
<meta property="og:type" content="website"><meta property="og:site_name" content="SAAFAAR HOLIDAYS">
<meta property="og:title" content="<?=e($title)?>"><meta property="og:description" content="<?=e($desc)?>">
<meta property="og:url" content="<?=e($canonical)?>"><meta property="og:image" content="<?=e($imageUrl)?>">
<meta name="twitter:card" content="summary_large_image"><meta name="theme-color" content="#f8f9fb">
<link rel="icon" type="image/svg+xml" href="/favicon.svg"><link rel="icon" href="/favicon.ico">
<link rel="apple-touch-icon" href="/apple-touch-icon.png"><link rel="manifest" href="/site.webmanifest">
<link rel="stylesheet" href="/assets/css/aether.css?v=7">
<script type="application/ld+json"><?=json_ld($organization)?></script>
</head><body>
<a class="skip-link" href="#main">Skip to main content</a>
<?php
}
function site_header(string $active=''):void {
 $nav=[['/','Home','home'],['/cars/','The garage','car-front'],['/search/','Find a car','search'],['/travel-guides/','Travel edit','compass'],['/northeast-india/','Destinations','map'],['/contact/','Contact','mail']];
?>
<div class="app-shell">
<aside class="rail" aria-label="Site navigation">
<a class="rail-logo" href="/" aria-label="SAAFAAR home"><img src="/assets/img/saafaar-logo.webp" width="110" height="38" alt="SAAFAAR"></a>
<nav class="rail-links">
<?php foreach($nav as [$url,$label,$symbol]):?>
<a href="<?=e($url)?>" class="rail-link<?=$active===$url?' is-active':''?>" aria-label="<?=e($label)?>" title="<?=e($label)?>">
<span class="rail-symbol" aria-hidden="true"><?=svg_icon($symbol)?></span><span class="rail-label"><?=e($label)?></span></a>
<?php endforeach; ?>
</nav>
<div class="rail-bottom"><a class="rail-link" href="/manage-booking/" title="Manage booking"><?=svg_icon('ticket')?><span class="rail-label">My booking</span></a></div>
</aside>
<div class="site-column">
<header class="topbar">
<a class="mobile-brand" href="/"><img src="/assets/img/saafaar-logo.webp" width="118" height="40" alt="SAAFAAR"></a>
<div class="eyebrow topbar-eyebrow">INDEPENDENT SELF-DRIVE EXPERIENCES <span class="tiny-dot"></span> GUWAHATI</div>
<div class="top-actions"><a class="location-pill" href="/guwahati-airport-car-rental/"><?=svg_icon('pin')?> <span>Guwahati, India</span></a>
<a class="top-whatsapp" href="https://wa.me/919181628882" target="_blank" rel="noopener"><?=svg_icon('message')?> <span>WhatsApp</span></a>
<button id="menuToggle" class="mobile-toggle" type="button" aria-label="Open navigation" aria-expanded="false"><?=svg_icon('menu')?></button></div>
</header>
<nav id="mobileMenu" class="mobile-menu" aria-label="Mobile navigation" hidden>
<?php foreach($nav as [$url,$label]):?><a href="<?=e($url)?>"><?=e($label)?></a><?php endforeach;?><a href="/manage-booking/">Manage booking</a>
</nav>
<main id="main">
<?php
}
function site_footer():void {?>
</main>
<footer class="footer">
<div><img src="/assets/img/saafaar-logo.webp" alt="SAAFAAR" width="130" height="48" loading="lazy"><p>Independent self-drive rental enquiries and road-trip inspiration from Guwahati.</p></div>
<div><strong>Explore</strong><a href="/cars/">Approved cars</a><a href="/travel-guides/">Travel guides</a><a href="/northeast-india/">Northeast India</a><a href="/guwahati-airport-car-rental/">Airport pickup</a></div>
<div><strong>Support</strong><a href="/contact/">Contact</a><a href="/faq/">FAQs</a><a href="/manage-booking/">Manage booking</a><a href="/rental-terms/">Rental terms</a><a href="/privacy-policy/">Privacy</a></div>
<div><strong>Get in touch</strong><a href="tel:+919181628882">+91 91816 28882</a><a href="https://wa.me/919181628882" target="_blank" rel="noopener">WhatsApp support</a><span>Guwahati · Assam</span></div>
<div class="footer-legal">© <?=date('Y')?> SAAFAAR HOLIDAYS <span>Designed for the road ahead.</span></div>
</footer>
</div></div>
<script src="/assets/js/app.js" defer></script>
</body></html>
<?php }
function svg_icon(string $kind):string {
 $paths=[
 'home'=>'<path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z"/><path d="M9 21v-8h6v8"/>',
 'car-front'=>'<path d="m5 17-1 3m15-3 1 3M5 10l2-6h10l2 6M4 10h16v8H4z"/><path d="M6 14h2m8 0h2"/>',
 'search'=>'<circle cx="10.8" cy="10.8" r="7"/><path d="m16 16 5 5"/>',
 'compass'=>'<circle cx="12" cy="12" r="9"/><path d="m16 8-6 2-2 6 6-2z"/>',
 'map'=>'<path d="M3 6 9 3l6 3 6-3v15l-6 3-6-3-6 3z"/><path d="M9 3v15m6-12v15"/>',
 'mail'=>'<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
 'ticket'=>'<path d="M4 6h16v4a2 2 0 0 0 0 4v4H4v-4a2 2 0 0 0 0-4z"/><path d="M12 6v12" stroke-dasharray="2 2"/>',
 'pin'=>'<path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0z"/><circle cx="12" cy="10" r="2.5"/>',
 'message'=>'<path d="M21 11a8 8 0 0 1-8 8H6l-3 2 1-5a8 8 0 1 1 17-5z"/>',
 'menu'=>'<path d="M4 6h16M4 12h16M4 18h16"/>',
 'arrow'=>'<path d="M5 12h14m-7-7 7 7-7 7"/>',
 'calendar'=>'<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4m10-4v4M3 10h18"/>',
 'shield'=>'<path d="m12 2 9 4v6c0 5-3.4 8-9 10-5.6-2-9-5-9-10V6z"/><path d="m9 12 2 2 4-4"/>'
 ];
 return '<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.($paths[$kind]??$paths['arrow']).'</svg>';
}
function search_form(array $a=[],bool $compact=false):void {
 $start=(string)($a['start_date']??'');$end=(string)($a['end_date']??'');
 $st=(string)($a['start_time']??'10:00');$et=(string)($a['end_time']??'10:00');
?>
<form class="search-grid<?=$compact?' search-grid-compact':''?>" action="/search/" method="get" id="rentalSearch" data-search>
<label><span>Pick-up location</span><select required name="pickup"><?php foreach(locations() as $loc):?><option value="<?=e($loc)?>"<?=($a['pickup']??'Guwahati Airport')===$loc?' selected':''?>><?=e($loc)?></option><?php endforeach;?></select></label>
<label><span>Return location</span><select required name="dropoff"><?php foreach(locations() as $loc):?><option value="<?=e($loc)?>"<?=($a['dropoff']??'Guwahati Airport')===$loc?' selected':''?>><?=e($loc)?></option><?php endforeach;?></select></label>
<label><span>Pick-up date</span><input type="date" required name="start_date" min="<?=date('Y-m-d')?>" value="<?=e($start)?>"></label>
<label><span>Pick-up time</span><input type="time" required name="start_time" value="<?=e($st)?>"></label>
<label><span>Return date</span><input type="date" required name="end_date" min="<?=date('Y-m-d')?>" value="<?=e($end)?>"></label>
<label><span>Return time</span><input type="time" required name="end_time" value="<?=e($et)?>"></label>
<button class="btn btn-dark search-submit" type="submit">Search eligible cars <?=svg_icon('arrow')?></button>
<p class="form-hint" role="status" data-search-error>Vehicle requests are reviewed by SAAFAAR before confirmation.</p>
</form>
<?php }
function section_head(string $kicker,string $title,string $body=''):void {?>
<div class="section-heading"><div><span class="eyebrow"><?=e($kicker)?></span><h2><?=e($title)?></h2><?php if($body!==''):?><p><?=e($body)?></p><?php endif;?></div></div><?php }
function home_page():void {
 $featured=array_slice(approved_vehicles(),0,3);
 head_html('Self Drive Car Rental in Guwahati | SAAFAAR HOLIDAYS','Find approved self-drive rental cars in Guwahati with SAAFAAR. Explore airport pick-up enquiries, automatic cars, SUVs and Northeast road trips.','/','meghalaya-hills.webp');
 site_header('/');
 ?>
<div class="home-hero">
<div class="hero-topline"><span class="eyebrow">THE SAAFAAR JOURNAL / 001</span><span class="topline-note">YOUR NEXT DRIVE BEGINS HERE</span></div>
<div class="hero-layout">
<div class="hero-visual">
<img src="/assets/img/meghalaya-hills.webp" width="1600" height="1000" alt="Meghalaya hills and waterfalls along a Northeast India road trip" fetchpriority="high">
<div class="hero-glow"></div>
<div class="hero-copy"><span class="eyebrow hero-tag">BUILT FOR THE BEAUTIFUL DETOUR</span>
<h1>The road is yours.<br><em>Go a little further.</em></h1><p>Self-drive car rental enquiries in Guwahati. More freedom for the open road, with the details carefully considered.</p>
<a class="btn btn-white" href="/northeast-india/">Explore Northeast India <?=svg_icon('arrow')?></a></div>
<div class="image-credit">ASSAM · MEGHALAYA · ARUNACHAL</div></div>
<div class="hero-book">
<div class="booking-panel-header"><div><span class="eyebrow">FIND YOUR DRIVE</span><h2>Where to next?</h2><p>Tell us your dates. We’ll show verified listings when available.</p></div><span class="booking-roundel"><?=svg_icon('car-front')?></span></div>
<?php search_form(); ?>
<div class="panel-trust"><?=svg_icon('shield')?><p>Every booking is reviewed. No false availability or instant payment claims.</p></div>
</div></div></div>
<div class="metrics-band"><div><strong>01</strong><span>Choose your journey</span></div><div><strong>02</strong><span>Review approved cars</span></div><div><strong>03</strong><span>Request a booking</span></div><div><strong>04</strong><span>Get staff confirmation</span></div></div>
<section class="section-block" id="garage"><?php section_head('THE SAAFAAR GARAGE','The right car changes everything.','Browse individually approved vehicles, or send us your preferred model and dates.');?>
<?php if($featured):?><div class="vehicle-grid"><?php foreach($featured as $v)car_card($v);?></div><?php else:?>
<div class="empty-fleet"><div class="empty-art"><span class="art-rings"></span><?=svg_icon('car-front')?></div><div><span class="eyebrow">GARAGE IN PREPARATION</span><h3>Every detail, verified before it goes live.</h3><p>Vehicle photographs, variants, pricing and availability will appear here once approved in the independent SAAFAAR inventory.</p><a class="btn btn-dark" href="/enquire/">Request a specific vehicle <?=svg_icon('arrow')?></a></div></div>
<?php endif;?><div class="section-bottom"><a class="text-link" href="/cars/">Discover the garage <?=svg_icon('arrow')?></a></div></section>
<section class="section-block"><?php section_head('CURATED FOR THE ROAD','Different journeys. Different drives.','Compare types by comfort and group size; final availability depends on approved vehicles.');?>
<div class="type-grid">
<a href="/automatic-car-rental-guwahati/" class="type-card"><span class="eyebrow">EFFORTLESS CITY DRIVING</span><h3>Automatics</h3><p>Relax into stop-and-go city travel and longer itineraries.</p><span class="type-arrow"><?=svg_icon('arrow')?></span></a>
<a href="/suv-rental-guwahati/" class="type-card"><span class="eyebrow">ROOM TO EXPLORE</span><h3>Premium SUVs</h3><p>Explore category options for luggage, family travel and mountain routes.</p><span class="type-arrow"><?=svg_icon('arrow')?></span></a>
<a href="/seven-seater-car-rental-guwahati/" class="type-card"><span class="eyebrow">EVERYONE ON BOARD</span><h3>Seven seaters</h3><p>Consider extra passenger room for your group itinerary.</p><span class="type-arrow"><?=svg_icon('arrow')?></span></a>
</div></section>
<section class="section-block"><?php section_head('THE ROAD TRIP EDIT','Not all good things are destinations.','Inspiration for unhurried days in Northeast India.');?>
<div class="destination-grid">
<a class="destination-card destination-large" href="/meghalaya-self-drive-car-rental/"><img src="/assets/img/meghalaya-waterfall.webp" width="1600" height="1000" loading="lazy" alt="Waterfall in Meghalaya"><span class="destination-overlay"><small>01 / MEGHALAYA</small><strong>Into the green.</strong><span>Shillong · Sohra · waterfalls <?=svg_icon('arrow')?></span></span></a>
<a class="destination-card" href="/arunachal-pradesh-road-trip/"><img src="/assets/img/arunachal-mountains.webp" width="1024" height="720" loading="lazy" alt="Arunachal Pradesh mountain scenery"><span class="destination-overlay"><small>02 / ARUNACHAL</small><strong>Follow the mountains.</strong><span>Tawang · Dirang · Bomdila <?=svg_icon('arrow')?></span></span></a>
<a class="destination-card" href="/northeast-india/"><img src="/assets/img/kaziranga-rhino.webp" width="1600" height="900" loading="lazy" alt="Rhinoceros in Assam’s Kaziranga landscape"><span class="destination-overlay"><small>03 / ASSAM</small><strong>Closer to the wild.</strong><span>Kaziranga and beyond <?=svg_icon('arrow')?></span></span></a></div></section>
<section class="section-block split-feature"><div><span class="eyebrow">ARRIVE. COLLECT. EXPLORE.</span><h2>From Guwahati Airport to wherever you dream of going.</h2><p>Plan an airport collection enquiry, compare actual vehicle availability and get your handover details reviewed before arrival.</p><a class="btn btn-dark" href="/guwahati-airport-car-rental/">Airport rentals <?=svg_icon('arrow')?></a></div><div class="split-image"><img src="/assets/img/arunachal-mountains.webp" width="1024" height="720" alt="Mountain valley in Arunachal Pradesh" loading="lazy"></div></section>
<section class="section-block"><?php section_head('STORIES FROM THE ROAD','The travel edit.','Useful travel planning ideas, written for the realities of Northeast journeys.');?>
<div class="articles-grid"><?php foreach(array_slice(guide_articles(),0,3,true) as $slug=>$a):?><a class="article-card" href="/travel-guides/<?=e($slug)?>/"><img src="<?=e(page_image($a['image']))?>" alt="<?=e($a['h1'])?>" width="550" height="360" loading="lazy"><div><span class="eyebrow">FIELD NOTES</span><h3><?=e($a['h1'])?></h3><span class="text-link">Read the story <?=svg_icon('arrow')?></span></div></a><?php endforeach;?></div>
<div class="section-bottom"><a class="text-link" href="/travel-guides/">All travel guides <?=svg_icon('arrow')?></a></div></section>
<section class="cta-banner"><span class="eyebrow">SAAFAAR HOLIDAYS</span><h2>Good roads make<br><em>great stories.</em></h2><p>Tell us where you want to go. We'll help with an enquiry based on verified options.</p><a class="btn btn-white" href="/contact/">Start a conversation <?=svg_icon('arrow')?></a></section>
<?php site_footer();
}
function car_card(array $v):void {?>
<article class="car-card">
<a class="car-photo" href="/cars/<?=e($v['slug'])?>/">
<?php if(!empty($v['cover'])):?><img src="<?=e($v['cover'])?>" width="720" height="480" loading="lazy" alt="<?=e($v['make'].' '.$v['model'])?>"><?php else:?><div class="vehicle-placeholder"><?=svg_icon('car-front')?><span>Photo under approval</span></div><?php endif;?>
<span class="car-tag"><?=e($v['category'])?></span></a>
<div class="car-details"><div class="car-name"><h3><a href="/cars/<?=e($v['slug'])?>/"><?=e(trim($v['make'].' '.$v['model']))?></a></h3><span><?=e((string)($v['year']??''))?></span></div>
<p><?=e((string)$v['transmission'])?> · <?=e((string)$v['fuel'])?> · <?=e((string)$v['seats'])?> seats</p>
<div class="car-bottom"><div><small>EST. PER DAY</small><strong><?=fmt_money($v['daily_rate'])?></strong></div><a class="btn btn-dark" href="/cars/<?=e($v['slug'])?>/">Details <?=svg_icon('arrow')?></a></div>
</div></article><?php }
function title_banner(string $eyebrow,string $title,string $intro,string $image='meghalaya-hills.webp'):void {?>
<div class="inner-banner"><div class="inner-banner-copy"><span class="eyebrow"><?=e($eyebrow)?></span><h1><?=e($title)?></h1><p><?=e($intro)?></p></div><div class="inner-banner-photo"><img src="<?=e(page_image($image))?>" alt="" width="1000" height="650" loading="eager"></div></div>
<?php }
function cars_page():void {
 $category=(string)($_GET['category']??'');$transmission=(string)($_GET['transmission']??'');
 if(!in_array($category,['','Hatchback','Sedan','Compact SUV','SUV','Seven Seater'],true))$category='';
 if(!in_array($transmission,['','Automatic','Manual'],true))$transmission='';
 $cars=approved_vehicles($category?:null,$transmission?:null);
 head_html('Approved Self Drive Cars in Guwahati | SAAFAAR','Browse SAAFAAR’s individually approved self-drive car listings by category and transmission. Request dates and a reviewed quote.','/cars/');
 site_header('/cars/');
 title_banner('THE GARAGE','Find your kind of freedom.','Explore approved self-drive listings. Search filters never show unverified fleet claims.');
 ?>
<section class="section-block"><div class="catalogue-toolbar"><div><span class="eyebrow">LIVE APPROVED LISTINGS</span><h2>Choose your next drive</h2></div><form method="get" action="/cars/" class="filters"><label>Category <select name="category"><option value="">All categories</option><?php foreach(['Hatchback','Sedan','Compact SUV','SUV','Seven Seater'] as $c):?><option <?=($category===$c?'selected':'')?> value="<?=e($c)?>"><?=e($c)?></option><?php endforeach;?></select></label><label>Transmission <select name="transmission"><option value="">Any transmission</option><option <?=($transmission==='Automatic'?'selected':'')?>>Automatic</option><option <?=($transmission==='Manual'?'selected':'')?>>Manual</option></select></label><button class="btn btn-dark" type="submit">Apply</button></form></div>
<?php if($cars):?><div class="vehicle-grid"><?php foreach($cars as $v)car_card($v);?></div><?php else:?><div class="info-empty"><h3>No approved cars match this view yet.</h3><p>Listings appear only after SAAFAAR staff approve each vehicle’s photographs, specifications and rates. You can still send a vehicle enquiry.</p><a class="btn btn-dark" href="/enquire/">Request a vehicle <?=svg_icon('arrow')?></a></div><?php endif;?></section>
<?php site_footer();
}
function vehicle_detail_page(string $slug):void {
 $v=vehicle_by_slug($slug);if(!$v){not_found_page();return;}
 $title=trim($v['make'].' '.$v['model'].' '.$v['variant']).' Self Drive Guwahati | SAAFAAR';
 head_html($title,'Explore approved vehicle details and request the '.trim($v['make'].' '.$v['model']).' for a self-drive rental in Guwahati.','/cars/'.$slug.'/');
 site_header('/cars/');?>
<div class="vehicle-heading"><span class="eyebrow">THE GARAGE / <?=e(strtoupper($v['category']))?></span><h1><?=e(trim($v['make'].' '.$v['model']))?><span> <?=e((string)$v['variant'])?></span></h1><p>Approved listing · request dates to check eligibility and availability</p></div>
<div class="vehicle-detail-layout"><div>
<?php $images=gallery((int)$v['id']);if($images):?><div class="gallery-main"><img id="galleryMain" src="<?=e($images[0]['file_path'])?>" alt="<?=e($images[0]['alt_text']?:($v['make'].' '.$v['model']))?>" width="1100" height="720"></div><div class="gallery-thumbs"><?php foreach($images as $im):?><button type="button" data-gallery-src="<?=e($im['file_path'])?>" data-gallery-alt="<?=e($im['alt_text'])?>"><img src="<?=e($im['file_path'])?>" alt="<?=e($im['alt_text'])?>" loading="lazy" width="160" height="110"></button><?php endforeach;?></div><?php else:?><div class="gallery-main vehicle-placeholder"><?=svg_icon('car-front')?><p>Real vehicle images are awaiting approval.</p></div><?php endif;?>
<div class="spec-grid"><div><small>Transmission</small><strong><?=e($v['transmission'])?></strong></div><div><small>Seats</small><strong><?=e((string)$v['seats'])?></strong></div><div><small>Fuel</small><strong><?=e((string)$v['fuel'])?></strong></div><div><small>Model year</small><strong><?=e((string)$v['year'])?></strong></div></div>
<?php if(!empty($v['notes'])):?><div class="content-paper"><h2>Vehicle notes</h2><p><?=nl2br(e($v['notes']))?></p></div><?php endif;?></div>
<aside class="vehicle-book-panel"><span class="eyebrow">REQUEST THIS DRIVE</span><h2><?=fmt_money($v['daily_rate'])?><span> / estimated day</span></h2><p>Final quotation depends on dates, location, available units and approved rental terms.</p><div class="vehicle-fee-row">Included distance <strong><?=($v['included_km']===null?'On enquiry':e((string)$v['included_km']).' km')?></strong></div><div class="vehicle-fee-row">Minimum duration <strong><?=e((string)$v['min_days'])?> day(s)</strong></div><a class="btn btn-white btn-full" href="/request-booking/?vehicle_id=<?=(int)$v['id']?>">Request booking <?=svg_icon('arrow')?></a><a class="button-secondary-dark" href="<?=e(whatsapp('Hello SAAFAAR HOLIDAYS, I would like information about '.trim($v['make'].' '.$v['model']).'.'))?>" target="_blank" rel="noopener">Enquire on WhatsApp</a><small class="panel-fine">Submitting a request is not a confirmed reservation.</small></aside></div>
<?php site_footer();
}
function search_page():void {
 $hasDates=isset($_GET['start_date'])||isset($_GET['end_date']);$period=$hasDates?rental_window($_GET):null;
 $locs=valid_location((string)($_GET['pickup']??''))&&valid_location((string)($_GET['dropoff']??''));
 $cars=($period&&$locs)?eligible_vehicles($period):[];
 head_html('Search Self Drive Car Availability | SAAFAAR','Search individually approved car rental options for dates and pickup locations in Guwahati.','/search/','meghalaya-hills.webp',true);
 site_header('/search/');
 title_banner('THE RENTAL SEARCH','The right car, for the right days.','Enter dates and locations to check eligibility against approved SAAFAAR vehicle records.');
 ?><section class="section-block"><div class="search-page-form"><?php search_form($_GET,true);?></div>
<?php if($hasDates&&!$period):?><div class="notice notice-error">Select future dates and ensure your return is later than your pickup. Maximum search duration: 90 days.</div>
<?php elseif($hasDates&&!$locs):?><div class="notice notice-error">Select supported pickup and return locations.</div>
<?php elseif($period):?><div class="results-heading"><div><span class="eyebrow">REVIEWED SEARCH</span><h2><?=count($cars)?> eligible listing<?=count($cars)===1?'':'s'?></h2><p><?=e($period[0]->format('j M Y, g:i A'))?> to <?=e($period[1]->format('j M Y, g:i A'))?></p></div></div>
<?php if($cars):?><div class="vehicle-grid"><?php foreach($cars as $v)car_card($v);?></div><p class="fine-print">Eligibility is checked against recorded bookings and blocks. Staff approval is required; a search result is not final confirmation.</p>
<?php else:?><div class="info-empty"><h3>There are no confirmed eligible listings to show.</h3><p>The inventory may still be awaiting approval, or the vehicles may be blocked for these dates. We cannot promise availability without a database-backed listing.</p><a class="btn btn-dark" href="/enquire/">Ask about an alternative <?=svg_icon('arrow')?></a></div><?php endif;?>
<?php endif;?></section><?php site_footer();
}
function request_booking_page():void {
 $id=(int)($_GET['vehicle_id']??0);$v=vehicle_by_id($id);
 head_html('Request a Self Drive Booking | SAAFAAR','Submit a SAAFAAR booking request for an approved vehicle. Staff confirmation is required.','/request-booking/','meghalaya-hills.webp',true);
 site_header('/search/');
 title_banner('REQUEST A BOOKING','Make the next journey yours.','Submit dates and contact details. You will receive an acknowledgement, not an automatic reservation.');
 if(!$v):?><div class="section-block info-empty"><h2>Select an approved car first.</h2><p>Requests can only be submitted against actual approved inventory. Browse the garage or send a general enquiry.</p><a class="btn btn-dark" href="/cars/">Explore cars <?=svg_icon('arrow')?></a> <a class="text-link" href="/enquire/">Send an enquiry</a></div><?php site_footer();return;}?>
<section class="section-block"><div class="booking-form-layout"><div class="form-card"><span class="eyebrow">YOUR REQUEST</span><h2><?=e($v['make'].' '.$v['model'])?></h2><p>Provide complete details and double-check the dates. Pricing shown is provisional until reviewed.</p>
<form method="post" action="/request-booking/" class="stacked-form">
<input type="hidden" name="_csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="vehicle_id" value="<?=(int)$v['id']?>">
<div class="form-row"><label>Your full name<input name="name" required maxlength="100" autocomplete="name"></label><label>Mobile number<input name="phone" required inputmode="tel" pattern="[6-9][0-9]{9}" placeholder="10-digit Indian mobile number"></label></div>
<label>Email (optional)<input name="email" type="email" autocomplete="email" maxlength="200"></label>
<div class="form-row"><label>Pickup<select name="pickup" required><?php foreach(locations() as $loc):?><option><?=e($loc)?></option><?php endforeach;?></select></label><label>Return<select name="dropoff" required><?php foreach(locations() as $loc):?><option><?=e($loc)?></option><?php endforeach;?></select></label></div>
<div class="form-row"><label>Pickup date<input type="date" name="start_date" required min="<?=date('Y-m-d')?>"></label><label>Pickup time<input type="time" name="start_time" value="10:00" required></label></div>
<div class="form-row"><label>Return date<input type="date" name="end_date" required min="<?=date('Y-m-d')?>"></label><label>Return time<input type="time" name="end_time" value="10:00" required></label></div>
<label class="checkbox"><input type="checkbox" required> I understand this is a request, not a confirmed booking, and that final terms will be reviewed.</label>
<button class="btn btn-dark btn-full" type="submit">Submit booking request <?=svg_icon('arrow')?></button>
</form></div>
<div class="booking-aside"><span class="eyebrow">SELECTED VEHICLE</span><h3><?=e($v['make'].' '.$v['model'])?></h3><p><?=e($v['category'])?> · <?=e($v['transmission'])?></p><div class="vehicle-fee-row">Provisional daily rate <strong><?=fmt_money($v['daily_rate'])?></strong></div><div class="vehicle-fee-row">Included kilometres <strong><?=($v['included_km']===null?'On request':e((string)$v['included_km']))?></strong></div><p class="fine-print">The final quote may include approved delivery, deposit, taxes or other charges, as disclosed before confirmation.</p></div></div></section>
<?php site_footer();
}
function booking_confirmation_page():void {
 $ref=(string)($_GET['ref']??'');$state=$_SESSION['last_booking']??null;
 $b=($state&&hash_equals($state['ref'],$ref))?booking_for_customer($ref,$state['secret']):null;
 head_html('Booking Request Acknowledgement | SAAFAAR','Private booking request acknowledgement.','/booking-confirmation/','meghalaya-hills.webp',true);
 site_header('');
 if(!$b):?><div class="section-block info-empty"><h1>Booking details are private.</h1><p>Open this page from your original booking session, or use Manage Booking with your private access code.</p><a class="btn btn-dark" href="/manage-booking/">Manage booking</a></div><?php site_footer();return;}?>
<div class="section-block"><div class="confirmation-card"><span class="status-pill">REQUEST RECEIVED · PENDING REVIEW</span><h1>You're on the right road.</h1><p>We’ve recorded your request under reference <strong><?=e($ref)?></strong>. It is <strong>not confirmed</strong> until staff complete their review.</p>
<div class="reference-box"><span>BOOKING REFERENCE</span><strong><?=e($ref)?></strong></div>
<div class="reference-box"><span>PRIVATE ACCESS CODE — SAVE THIS</span><strong class="secret-code"><?=e($state['secret'])?></strong></div>
<p class="fine-print">Store your private code securely. It is required with your booking reference to view the booking later. Do not share it publicly.</p>
<div class="confirmation-actions"><a class="btn btn-dark" href="<?=e(whatsapp("Hello SAAFAAR HOLIDAYS, I submitted booking request {$ref}. Kindly review and confirm the details."))?>" target="_blank" rel="noopener">Continue on WhatsApp <?=svg_icon('arrow')?></a><a class="btn btn-outline" href="/manage-booking/">Manage booking</a></div></div></div>
<?php site_footer();
}
function manage_booking_page():void {
 $b=null;$error='';
 if(is_post()&&isset($_POST['lookup'])){$ref=input('reference',32);$secret=input('access',90);$b=booking_for_customer($ref,$secret);if(!$b)$error='No matching booking was found for these secure details.';}
 if(is_post()&&isset($_POST['submit_utr']))handle_utr();
 head_html('Manage My Rental Booking | SAAFAAR','Securely look up your rental request status.','/manage-booking/','meghalaya-hills.webp',true);
 site_header('');title_banner('PRIVATE BOOKING ACCESS','Your journey. Your details.','Use your booking reference and private access code to view progress.');
 ?><section class="section-block"><div class="form-card narrow-form"><form action="/manage-booking/" method="post" class="stacked-form">
<input type="hidden" name="_csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="lookup" value="1">
<label>Booking reference<input name="reference" required maxlength="32" value="<?=e((string)($_POST['reference']??''))?>" placeholder="SF…"></label>
<label>Private access code<input name="access" required maxlength="90" autocomplete="off"></label>
<button type="submit" class="btn btn-dark">View booking <?=svg_icon('arrow')?></button>
</form><?php if($error):?><p class="notice notice-error"><?=e($error)?></p><?php endif;?></div>
<?php if($b):?><div class="booking-record"><span class="status-pill"><?=e(strtoupper($b['status']))?> · <?=e(strtoupper(str_replace('_',' ',$b['payment_status'])))?></span><h2><?=e($b['make'].' '.$b['model'])?></h2>
<div class="record-grid"><div><small>Reference</small><strong><?=e($b['reference'])?></strong></div><div><small>Estimated total</small><strong><?=fmt_money($b['estimated_total'])?></strong></div><div><small>Pickup</small><strong><?=e($b['pickup_location'].' | '.$b['pickup_at'])?></strong></div><div><small>Return</small><strong><?=e($b['return_location'].' | '.$b['return_at'])?></strong></div></div>
<p>Confirmation only follows staff approval. Submitting payment references never automatically verifies funds.</p>
<?php if(setting('PAYMENTS_ENABLED')==='1'):?><form method="post" class="stacked-form"><input type="hidden" name="_csrf" value="<?=e(csrf_token())?>"><input type="hidden" name="submit_utr" value="1"><input type="hidden" name="reference" value="<?=e($b['reference'])?>"><input type="hidden" name="access" value="<?=e(input('access',90))?>"><label>Submit payment transaction reference (UTR)<input name="utr" required pattern="[A-Za-z0-9-]{8,40}"></label><button class="btn btn-dark" type="submit">Submit for staff verification</button></form><?php endif;?>
</div><?php endif;?></section><?php site_footer();
}
function contact_page(bool $enquiry=false):void {
 head_html($enquiry?'Send a Vehicle Enquiry | SAAFAAR':'Contact SAAFAAR HOLIDAYS | Guwahati','Contact SAAFAAR HOLIDAYS for approved self-drive rental enquiries in Guwahati.',$enquiry?'/enquire/':'/contact/','meghalaya-hills.webp',$enquiry);
 site_header('/contact/');
 title_banner($enquiry?'YOUR VEHICLE ENQUIRY':'WE ARE HERE TO HELP',$enquiry?'Tell us your plans.':'Good journeys start with a conversation.','Share your preferred car, pickup, return dates and any route questions. We will review your enquiry.');
 ?><div class="section-block contact-layout"><div class="form-card"><span class="eyebrow">SEND AN ENQUIRY</span><h2>Where can we take you?</h2><form action="/enquire/" method="post" class="stacked-form"><input type="hidden" name="_csrf" value="<?=e(csrf_token())?>"><div class="form-row"><label>Your name<input name="name" required maxlength="100"></label><label>Mobile number<input name="phone" required pattern="[6-9][0-9]{9}" placeholder="10-digit Indian mobile number"></label></div><label>Tell us about your trip<textarea name="message" rows="6" minlength="10" required placeholder="Preferred car, pickup date/time, return date/time and destinations…"></textarea></label><button class="btn btn-dark" type="submit">Send enquiry <?=svg_icon('arrow')?></button></form>
<?php if(isset($_GET['submitted'])&&isset($_SESSION['enquiry_message'])):?><div class="notice">Your enquiry is ready. <a href="<?=e($_SESSION['enquiry_message'])?>" target="_blank" rel="noopener">Continue on WhatsApp to send the details</a>.</div><?php endif;?></div>
<div class="contact-card"><span class="eyebrow">DIRECT CONTACT</span><h3>SAAFAAR HOLIDAYS</h3><p>Guwahati, Assam, India</p><a href="tel:+919181628882" class="contact-big">+91 91816 28882</a><a href="https://wa.me/919181628882" target="_blank" rel="noopener" class="btn btn-white">Open WhatsApp <?=svg_icon('arrow')?></a><hr><p>Actual pickup services, rental terms, operating hours and vehicle availability require staff confirmation.</p></div></div>
<?php site_footer();
}
function guide_index_page():void {
 head_html('Northeast Road Trip Travel Guides | SAAFAAR','Explore original Northeast India road-trip planning guides for Meghalaya, Tawang, Kaziranga and Nagaland.','/travel-guides/','arunachal-mountains.webp');site_header('/travel-guides/');
 title_banner('THE TRAVEL EDIT','Stories for the road ahead.','Practical route-planning ideas and visual inspiration across Northeast India.','arunachal-mountains.webp');
 ?><section class="section-block"><div class="articles-grid"><?php foreach(guide_articles() as $slug=>$a):?><a class="article-card" href="/travel-guides/<?=e($slug)?>/"><img src="<?=e(page_image($a['image']))?>" width="550" height="360" loading="lazy" alt="<?=e($a['h1'])?>"><div><span class="eyebrow">THE TRAVEL EDIT</span><h3><?=e($a['h1'])?></h3><p><?=e($a['intro'])?></p><span class="text-link">Read guide <?=svg_icon('arrow')?></span></div></a><?php endforeach;?></div></section><?php site_footer();
}
function editorial_page(string $slug):void {
 $all=guide_articles();if(!isset($all[$slug])){not_found_page();return;}$a=$all[$slug];
 head_html($a['title'],$a['intro'],'/travel-guides/'.$slug.'/',$a['image']);site_header('/travel-guides/');
 title_banner('THE TRAVEL EDIT / FIELD NOTES',$a['h1'],$a['intro'],$a['image']);
 ?><div class="editorial-layout"><article class="article-body"><div class="article-meta">SAAFAAR EDITORIAL <span>·</span> JOURNEY PLANNING</div>
<?php foreach($a['sections'] as [$heading,$para]):?><h2><?=e($heading)?></h2><p><?=e($para)?></p><?php endforeach;?>
<div class="notice">Check the latest weather, road restrictions, official permits and local advice before travel. This article is planning guidance, not an access guarantee.</div>
<a class="btn btn-dark" href="/enquire/">Enquire about a drive <?=svg_icon('arrow')?></a></article>
<aside class="editorial-aside"><img src="<?=e(page_image($a['image']))?>" alt="" loading="lazy"><h3>More than the destination.</h3><p>Explore independently researched route-planning pages for Northeast India.</p><a class="text-link" href="/northeast-india/">Explore the region <?=svg_icon('arrow')?></a></aside></div>
<?php site_footer();
}
function info_page(string $slug):void {
 $all=public_pages();if(!isset($all[$slug])){not_found_page();return;}$a=$all[$slug];
 head_html($a['title'],$a['intro'],'/'.$slug.'/',$a['image']);site_header('');
 title_banner('SAAFAAR HOLIDAYS',$a['h1'],$a['intro'],$a['image']);
 ?><div class="editorial-layout"><article class="article-body"><?php foreach($a['sections'] as [$heading,$paragraph]):?><section><h2><?=e($heading)?></h2><p><?=e($paragraph)?></p></section><?php endforeach;?>
<div class="notice"><strong>Planning note:</strong> Vehicle availability, prices and travel permissions require verification. All booking requests are subject to staff confirmation.</div><div class="info-links"><a href="/cars/">Approved vehicles</a><a href="/search/">Search dates</a><a href="/contact/">Contact the team</a></div></article>
<aside class="editorial-aside"><img src="<?=e(page_image($a['image']))?>" alt="" loading="lazy"><span class="eyebrow">START YOUR JOURNEY</span><h3>One good drive can change the way you see a place.</h3><p>Discover approved vehicles or send us your preferred model and travel dates.</p><a class="btn btn-dark" href="/enquire/">Make an enquiry <?=svg_icon('arrow')?></a></aside></div>
<?php site_footer();
}
function offline_page():void {
 head_html('Offline | SAAFAAR','You appear to be offline.','/offline/','meghalaya-hills.webp',true);site_header('');
 ?><section class="section-block info-empty"><span class="eyebrow">CONNECTION LOST</span><h1>Even the best roads have a few stops.</h1><p>You're currently offline. Check your connection and reload when you're ready.</p><a class="btn btn-dark" href="/">Try again <?=svg_icon('arrow')?></a></section><?php site_footer();
}
function not_found_page():void {
 http_response_code(404);
 head_html('Page Not Found | SAAFAAR','This SAAFAAR page could not be found.','/404/','meghalaya-hills.webp',true);site_header('');
 ?><section class="section-block info-empty"><span class="eyebrow">404 / WRONG TURN</span><h1>That road doesn't go anywhere.</h1><p>The page may have moved or the address may be incorrect. Return to the beginning.</p><a class="btn btn-dark" href="/">Back to homepage <?=svg_icon('arrow')?></a> <a class="text-link" href="/cars/">Explore cars</a></section><?php site_footer();
}
