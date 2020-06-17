<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/omics/lib/database.php');

$total = 0;
$categories = [];
$ds=[];
$topic = strtolower(($_GET["topic"])?$_GET["topic"]:"rnaseq"); 

if($topic=="rna-seq") {
  $sql = <<<SQL
SELECT study_id, count(1) as num FROM rna_seq.samples group by study_id ORDER BY study_id ASC;
SQL;

  $query = new RawQuery('rnaseq', $sql);
  $results = $query->get();

  foreach($results as $result) {
    $categories[] = $result->study_id;
    $ds[] = $result->num;	
  }
} elseif ($topic == "methylation") {
  $categories = ["CAMP","CRA","Framingham","LTRC","MESA pilot","WHI"];
  $ds = [1616, 1238, 1814, 3051, 2980, 1334];
} elseif($topic == "metabolomics" ) {
  $categories = ["CAMP", "Framingham", "WHI"];
  $ds = [3015, 3026,1401];
} else {
  //bad request
  http_response_code(400);
  exit;
}
$json = array('category'=>$categories, 'dataset'=>$ds, 'total'=>number_format(array_sum($ds)));
echo json_encode($json); 

?>
