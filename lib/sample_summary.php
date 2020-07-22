<?php
require_once("/var/www/vendor/autoload.php");
require_once("/var/www/config.php");
require_once("/var/www/report/lib/database.php");

$total = 0;
$categories = [];
$studies = [];
$ds=[];
$topic = strtolower(($_GET["topic"])?$_GET["topic"]:"rna-seq"); 

if($topic=="rna-seq") {
  $results = Sample::selectRaw("study_id, count(1) as num")
    ->groupBy("study_id")
    ->orderBy("study_id")
    ->get();

  foreach($results as $result) {
    $study = Study::where("study", $result->study_id)
      ->first();
    $studies[] = $result->study_id;
    $categories[] = ($study)?$study->study." (".$study->center.")":$result->study_id;
    $ds[] = $result->num;
  }
} elseif ($topic == "methylation" || $topic == "metabolomics") {
  $results = Study::where("datatype", $topic)
    ->orderBy("study","ASC")
    ->get();
  foreach ($results as $result) {
    $categories[] = $result->study." (".$result->center.")";
    $ds[] = $result->samplereceived;
  }
} else {
  //bad request
  http_response_code(400);
  exit;
}
$json = array('category'=>$categories, 'dataset'=>$ds, 'total'=>number_format(array_sum($ds)), 'studies'=>$studies);
echo json_encode($json); 

?>
