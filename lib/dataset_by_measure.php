<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/omics/lib/database.php');

if(!$_GET['field'] || !$_GET['label'] || !$_GET['study']) {
  //bad request
  http_response_code(400);
  exit;
}

$label = $_GET['label'];
if($_GET['study'] === "all") {
$sql = <<<SQL
SELECT {$_GET['field']} as val FROM rna_seq.qc_metrics;
SQL;
} else {
$sql = <<<SQL
SELECT {$_GET['field']} as val FROM rna_seq.qc_metrics join samples
  on qc_metrics.sample_id = samples.id 
  WHERE study_id = "{$_GET['study']}";
SQL;
}

$query = new RawQuery('rnaseq', $sql);
if ($results = $query->get()) {
  $ds=[];
  foreach($results as $result) {
    $ds[] = $result->val;
  }
  print json_encode(["label"=>$label, "data" => $ds]);
} else {
  //bad request
  http_response_code(400);
  exit;
}
?>

