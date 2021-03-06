<?php
require_once("/var/www/vendor/autoload.php");
require_once("/var/www/config.php");
require_once("/var/www/report/lib/database.php");

$results = Sample::selectRaw("UNIX_TIMESTAMP(arrival_date) as d, count(id) as num")
  ->groupBy("d")
  ->orderBy("d", "ASC")
  ->get();

$ds=[];
$sum=0;
foreach($results as $result) {
  $sum += $result->num;
  $ds[] = [$result->d*1000, $sum];	
}

echo json_encode($ds);

?>
