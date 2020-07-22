<?php
require_once("/var/www/vendor/autoload.php");
require_once("/var/www/config.php");
require_once("/var/www/report/lib/database.php");

$results = Study::selectRaw("center, datatype, SUM(samplereceived) as num")
  ->groupBy("datatype")
  ->groupBy("center")
  ->get();

$ds=[];
foreach($results as $result) {
  $ds[] = $result;
}

echo json_encode($ds);

?>
