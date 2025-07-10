<?php
require_once("/var/www/vendor/autoload.php");
require_once("/var/www/config.php");
require_once("/var/www/report/lib/database.php");

$total = 0;
$categories = [];
$studies = [];
$ds=[];
$topic = strtolower(($_GET["topic"])?$_GET["topic"]:"rna-seq"); 

if (in_array($topic, ["rna-seq","methylation","metabolomics","proteomics"])) {
  $results = Study::where("datatype", $topic)
    ->orderBy("study","ASC")
    ->get();
  foreach ($results as $result) {
    $studies[] = $result->study;
    $categories[] = $result->study." (".$result->center.(($topic==="rna-seq")?"|".$result->pi:"").")";
    $ds[] = $result->samplereceived;
  }
} else {
  //bad request
  http_response_code(400);
  exit;
}
$json = array('category'=>$categories, 'dataset'=>$ds, 'total'=>number_format(array_sum($ds)), 'studies'=>array_unique($studies));
echo json_encode($json); 

?>
