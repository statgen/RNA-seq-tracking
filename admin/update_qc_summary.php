#!/usr/bin/php

<?php
require_once("../lib/cli.inc.php");
$field = "";

print "Updating ... \n";
$sql = <<<SQL
SELECT * FROM rna_seq.qc_attributes_mapper where field_name != "sample_id" order by id ASC;
SQL;

$conn = db_conn();
$result = $conn->query($sql);
while($row = $result->fetch_assoc()) {
  $field = $row["field_name"];
  $calSql= <<<SQL
SELECT {$field} from `qc_metrics` ORDER BY {$field} ASC;
SQL;
  
  $dataset = $conn->query($calSql);
  $array = [];
  while($ds = $dataset->fetch_assoc()) {
    $array[] = $ds[$field]; 	
  }
  $median = get_median($array);
  $pct25 = get_first_quartile($array);
  $pct75 = get_third_quartile($array);
  $sd = get_sd ($array, $bSample = false);
  $mean = array_sum($array) / count($array);
  $min = min($array);
  $max = max($array);
  
  $updateSql = <<<SQL
UPDATE `rna_seq`.`qc_attributes_mapper` SET `mean`=$mean, `sd`=$sd, `min`=$min, `25pct`=$pct25, `50pct`=$median, `75pct`=$pct75, `max`=$max WHERE `field_name` = "{$field}";
SQL;
  if($conn->query($updateSql)) {
    echo "Finished updating the " .$field. " \n";
  }
}  

$conn->close();
  
function get_median ($dataset) {
  return (count($dataset) % 2) ? $dataset[(count($dataset) - 1) / 2] : ($dataset[count($dataset) / 2 + 1] + $dataset[count($dataset) / 2]) / 2;
}

function get_first_quartile($dataset) {
  return $dataset[floor(count($dataset) / 4)];
}

function get_third_quartile($dataset) {
  return $dataset[floor(count($dataset) * 3 / 4)];
}

function get_sd ($dataset, $bSample = false) {

  //The direct function below requires installing PECL package
  //return stats_standard_deviation($dataset);

  $fMean = array_sum($dataset) / count($dataset);
  $fVariance = 0.0;
  foreach ($dataset as $i)
  {
    $fVariance += pow($i - $fMean, 2);
  }
  $fVariance /= ( $bSample ? count($dataset) - 1 : count($dataset) );
  return (float) sqrt($fVariance);
}

?>
