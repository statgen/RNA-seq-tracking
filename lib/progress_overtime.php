<?php
require_once("/var/www/vendor/autoload.php");
require_once("/var/www/config.php");
require_once("/var/www/report/lib/database.php");

$results = QcMetrics::join("file_queue","file_queue.id","=","file_queue_id")
  ->selectRaw("Date(qc_date) as d, count(1) as num")
  ->groupBy("qc_date")
  ->orderBy("qc_date")
  ->get();

$categories = [];
$ds=[];
foreach($results as $result) {
  $categories[] = $result->d;
  $ds[] = $result->num;	
}

$json = array('category'=>$categories, 'data'=>$ds);
echo json_encode($json); 

?>
