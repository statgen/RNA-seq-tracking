<?php

require_once("/var/www/vendor/autoload.php");
require_once("/var/www/config.php");
require_once("/var/www/report/lib/database.php");

$ds=[];
$fields_not_display = ["sample_id","note"];
foreach(QcAttributesMapper::whereNotIn("field_name", $fields_not_display)
          ->orderBy("weight","DESC")
          ->get() as $result) {
  $result->boxPlot=[$result->min, $result->pct25, $result->median, $result->pct75, $result->max];
  $ds[] = $result;
}

print json_encode(["last_page"=>1, "data" => $ds]);
?>
