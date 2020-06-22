#!/usr/bin/php

<?php
require_once("../lib/cli.inc.php");
$field = "";

print "Updating ... \n";
foreach(QcAttributesMapper::where("field_name","!=","sample_id")->get() as $mapper) {
  $field = $mapper->field_name;
  $array = [];
  foreach(QcMetrics::select($field)->orderBy($field)->get() as $metrics){
    if(!empty($metrics->$field)) {
      $array[]=$metrics->$field;
    }
  }
  if(count($array)) {
    $mapper->median = get_median($array);
    $mapper->pct25 = get_first_quartile($array);
    $mapper->pct75 = get_third_quartile($array);
    $mapper->sd = get_sd ($array, $bSample = false);
    $mapper->mean = array_sum($array) / count($array);
    $mapper->min = min($array);
    $mapper->max = max($array);
  }

  if($mapper->save()) {
    echo "Finished updating the " .$field. " \n";
  }
}


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
