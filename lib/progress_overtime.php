<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/omics/lib/database.php');

$sql = <<<SQL
SELECT DATE(qc_date) as d, count(1) as num FROM qc_metrics m join rna_seq.file_queue q
  on m.file_queue_id = q.id 
  group by qc_date 
  order by d ASC;
SQL;

$query = new RawQuery('rnaseq', $sql);
$results = $query->get();

$categories = [];
$ds=[];
foreach($results as $result) {
  $categories[] = $result->d;
  $ds[] = $result->num;	
}

$json = array('category'=>$categories, 'data'=>$ds);
echo json_encode($json); 

?>
