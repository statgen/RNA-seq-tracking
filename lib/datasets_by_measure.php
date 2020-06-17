<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/omics/lib/database.php');

if(!$_GET['field'] || !$_GET['label'] || !$_GET['study']) {
  //bad request
  http_response_code(400);
  exit;
}

$label = $_GET['label'];

//query default dataset including all studies
if($_GET['study'] ==="all") {
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

$queryOne = new RawQuery('rnaseq', $sql);
if ($results = $queryOne->get()) {
  $ds=[];
  foreach($results as $result) {
    $ds[] = $result->val;
  }
}

//query dataset two if set
if(isset($_GET['compare'])) {
  $sqlTwo = <<<SQL
SELECT {$_GET['field']} as val FROM rna_seq.qc_metrics join samples
  on qc_metrics.sample_id = samples.id 
  WHERE study_id = "{$_GET['compare']}";
SQL;

  $queryTwo = new RawQuery('rnaseq', $sqlTwo);
  if ($rows = $queryTwo->get()) {
    $compare=[];
    foreach($rows as $row) {
      $compare[] = $row->val;
    }
  }
}

print json_encode(["label"=>$label, "data" => $ds, "compare" => $compare]);

?>

