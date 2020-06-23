<?php
if(!isset($_SESSION)){
  session_start();
}
if(!$_SESSION['access_token'] OR !$_SESSION['omics_user']) {
  //Unauthorized
  http_response_code(401);
  exit;
}

require_once("/var/www/vendor/autoload.php");
require_once("/var/www/config.php");
require_once("/var/www/report/lib/database.php");

ini_set('memory_limit', '2G');
ini_set("auto_detect_line_endings", true);

//build header array
$header = [
  "sample_id" => "Sample ID",
  "study_id" => "Study",
  "dataset_id" => "Dataset"
];

foreach(QcAttributesMapper::select("field_name","full_attribute")->where("weight","1")->get() as $mapper) {
  $header[$mapper->field_name] = $mapper->full_attribute;	
}

$metrics = QcMetrics::join("samples","samples.id","=","sample_id")
  ->select("qc_metrics.*", "study_id", "dataset_id")
  ->get();

$today = date("Y-m-d-His");

//output headers so that the file is downloaded rather than displayed  
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=RNA_seq_metrics_'.$today.'.tab');

$output = fopen('php://output', 'wt');

//output header
fputcsv($output, array_values($header), chr(9));

//output values by row
foreach($metrics as $row) {
 $data = [];
 foreach($header as $key=>$value) {
   $data[] = ($row->$key===null)?"NA":$row->$key;
 }
 fputcsv($output, $data, chr(9)); 
}

exit;
