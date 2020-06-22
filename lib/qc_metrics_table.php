<?php

require_once("/var/www/vendor/autoload.php");
require_once("/var/www/config.php");
require_once("/var/www/report/lib/database.php");

$ds=[];
foreach(QcAttributesMapper::where("field_name","!=","sample_id")->get() as $result) {
  $result->boxPlot=[$result->min, $result->pct25, $result->median, $result->pct75, $result->max];
  $ds[] = $result;
}

print json_encode(["last_page"=>1, "data" => $ds]);
?>
