<?php

session_start();
require_once($_SERVER['DOCUMENT_ROOT'] . '/omics/lib/database.php');

if(!$_SESSION['access_token'] OR !$_SESSION['omics_user']) {
  //Unauthorized
  http_response_code(401);
  exit;
}

ini_set('memory_limit', '2G');

//Check if there is a valid session
//if($_SESSION['access_token'] && $_SESSION['topmed_user']) {

ini_set("auto_detect_line_endings", true);

//build header array
$header = [
  "sample_id" => "Sample ID",
  "study_id" => "Study",
  "dataset_id" => "Dataset"
];
 
$sql = <<<SQL
SELECT field_name, full_attribute from qc_attributes_mapper where weight = 1 order by id ASC;
SQL;

$query = new RawQuery('rnaseq', $sql);
if ($results = $query->get()) {
  foreach($results as $result) {
    $header[$result->field_name] = $result->full_attribute;	
  }
}

$qcSql = <<<SQL
SELECT qc_metrics.*, samples.study_id, samples.dataset_id 
  FROM samples join qc_metrics 
  on samples.id = qc_metrics.sample_id;
SQL;

$qcQuery = new RawQuery('rnaseq', $qcSql);
$metrics = $qcQuery->get(); 

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
   $data[] = $row->$key;
 }
 fputcsv($output, $data, chr(9)); 
}

exit;
/*} else {
  http_response_code(403); //FORBIDDEN
}*/
