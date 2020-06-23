<?php
require_once("/var/www/vendor/autoload.php");
require_once("/var/www/config.php");
require_once("/var/www/report/lib/database.php");

if(!$_GET['field'] || !$_GET['label'] || !$_GET['study']) {
  //bad request
  http_response_code(400);
  exit;
}

$label = $_GET['label'];
$field = $_GET["field"];
$study = $_GET["study"];
$ds=[];
$compare=[];

//query default dataset including all studies
if($study ==="all") {
  $ds = QcMetrics::whereNotNull($field)->pluck($field)->toArray();
} else {
  $ds = QcMetrics::join("samples","samples.id","=","sample_id")
    ->whereNotNull($field)
    ->where("study_id",$study)
    ->pluck($field)
    ->toArray();
}

//query dataset two if set
if(isset($_GET['compare'])) {
  $compare = QcMetrics::join("samples","samples.id","=","sample_id")
    ->whereNotNull($field)
    ->where("study_id", $_GET['compare'])
    ->pluck($field)
    ->toArray();
}

print json_encode(["label"=>$label, "data" => $ds, "compare" => $compare]);

?>

