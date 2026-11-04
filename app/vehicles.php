<?php
require_once __DIR__.'/core.php';
function approved_vehicles(?string $category=null, ?string $transmission=null):array {
  $pdo=try_db();if(!$pdo)return [];
  $sql="SELECT v.*, (SELECT file_path FROM vehicle_images i WHERE i.vehicle_id=v.id ORDER BY sort_order,id LIMIT 1) AS cover FROM vehicles v WHERE v.status='active' AND v.approved=1";
  $args=[];
  if($category){$sql.=" AND v.category=?";$args[]=$category;}
  if($transmission){$sql.=" AND v.transmission=?";$args[]=$transmission;}
  $sql.=" ORDER BY v.featured DESC,v.id DESC";
  $s=$pdo->prepare($sql);$s->execute($args);return $s->fetchAll();
}
function vehicle_by_slug(string $slug):?array {
 $p=try_db();if(!$p)return null;
 $s=$p->prepare("SELECT * FROM vehicles WHERE slug=? AND status='active' AND approved=1 LIMIT 1");$s->execute([$slug]);return $s->fetch()?:null;
}
function vehicle_by_id(int $id):?array {
 $p=try_db();if(!$p)return null;
 $s=$p->prepare("SELECT * FROM vehicles WHERE id=? AND status='active' AND approved=1 LIMIT 1");$s->execute([$id]);return $s->fetch()?:null;
}
function gallery(int $id):array {
 $p=try_db();if(!$p)return [];
 $s=$p->prepare("SELECT file_path,alt_text FROM vehicle_images WHERE vehicle_id=? ORDER BY sort_order,id");$s->execute([$id]);return $s->fetchAll();
}
function availability_check(PDO $pdo,int $vehicleId,string $from,string $to):bool {
 $b=$pdo->prepare("SELECT COUNT(*) FROM bookings WHERE vehicle_id=? AND status IN ('confirmed','active') AND pickup_at < ? AND return_at > ?");
 $b->execute([$vehicleId,$to,$from]);
 if((int)$b->fetchColumn()>0)return false;
 $block=$pdo->prepare("SELECT COUNT(*) FROM vehicle_blocks WHERE vehicle_id=? AND starts_at < ? AND ends_at > ?");
 $block->execute([$vehicleId,$to,$from]);
 return (int)$block->fetchColumn()===0;
}
function eligible_vehicles(array $period):array {
 $pdo=try_db();if(!$pdo)return [];
 $all=approved_vehicles();$rows=[];
 foreach($all as $v){
  if(!availability_check($pdo,(int)$v['id'],$period[0]->format('Y-m-d H:i:s'),$period[1]->format('Y-m-d H:i:s')))continue;
  $rows[]=$v;
 }
 return $rows;
}
function rental_estimate(array $vehicle,DateTimeImmutable $start,DateTimeImmutable $end):array {
 // Provisional 24-hour blocks, shown as estimates only until SAAFAAR approves billing rules.
 $seconds=$end->getTimestamp()-$start->getTimestamp();
 $days=(int)ceil($seconds/86400);
 $min=max(1,(int)($vehicle['min_days']??1));
 if($days<$min)throw new InvalidArgumentException("Minimum rental period for this vehicle is {$min} days.");
 $rate=$vehicle['daily_rate']===null?null:(float)$vehicle['daily_rate'];
 return ['days'=>$days,'rate'=>$rate,'subtotal'=>$rate===null?null:$rate*$days,'estimate_only'=>true];
}
