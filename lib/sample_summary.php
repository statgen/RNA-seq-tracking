<?php
require_once("/var/www/vendor/autoload.php");
require_once("/var/www/config.php");
require_once("/var/www/report/lib/database.php");

$total = 0;
$categories = [];
$ds=[];
$topic = strtolower(($_GET["topic"])?$_GET["topic"]:"rnaseq"); 

if($topic=="rna-seq") {
  $results = Sample::selectRaw("study_id, count(1) as num")
    ->groupBy("study_id")
    ->orderBy("study_id")
    ->get();

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
