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
if($_GET['study'] ==="all") {
  $query = QcMetrics::whereNotNull($field)->select($field);
} else {
  $query = QcMetrics::join("samples","samples.id","=","sample_id")
    ->select($field)
    ->whereNotNull($field)
    ->where("study_id",$study);
}

$ds=[];
foreach($query->get() as $result) {
  $ds[] = $result->$field;
}

//query dataset two if set
if(isset($_GET['compare'])) {
  $sqlTwo = QcMetrics::join("samples","samples.id","=","sample_id")
    ->select($field)
    ->whereNotNull($field)
    ->where("study_id", $_GET['compare']);

  foreach($sqlTwo->get() as $row) {
    $compare[] = $row->$field;
  }
}

print json_encode(["label"=>$label, "data" => $ds, "compare" => $compare]);

?>

