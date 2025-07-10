<?php
require_once("/var/www/vendor/autoload.php");
require_once("/var/www/config.php");
require_once("/var/www/report/lib/database.php");

if(!$_GET['field'] || !$_GET['label'] || !$_GET['study']) {
  //bad request
  http_response_code(400);
  exit;
}

$label = strip_tags($_GET['label']);
$label = filter_var($label, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_HIGH);
$field = $_GET["field"];
$study = $_GET["study"];
$centers = ["Broad","NWGC","NYGC"];
$selectBy = in_array($study, $centers)?"center":"study_id";
$ds=[];
$compare=[];

//query default dataset including all studies
if($study ==="All studies") {
  $ds = QcMetrics::whereNotNull($field)->pluck($field)->toArray();
} else {
  $ds = QcMetrics::join("samples","samples.id","=","sample_id")
    ->whereNotNull($field)
    ->where($selectBy, $study)
    ->pluck($field)
    ->toArray();
}

//query dataset two if set
if(isset($_GET['compare'])) {
  $useField = in_array($_GET['compare'], $centers)?"center":"study_id";
  $compare = QcMetrics::join("samples","samples.id","=","sample_id")
    ->whereNotNull($field)
    ->where($useField, $_GET['compare'])
    ->pluck($field)
    ->toArray();
}

print json_encode(["label"=>$label, "data" => $ds, "compare" => $compare]);

?>

