<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/omics/lib/database.php');

$sql = <<<SQL
SELECT id, field_name, full_attribute, weight, mean, sd, min, 25pct as pct25, 50pct as median, 75pct as pct75, max FROM rna_seq.qc_attributes_mapper where field_name != "sample_id" AND mean is not NULL AND mean != 0 order by id;
SQL;

$query = new RawQuery('rnaseq', $sql);
$results = $query->get();

$ds=[];
foreach($results as $result) {
  $result->boxPlot=[$result->min, $result->pct25, $result->median, $result->pct75, $result->max];
  $ds[] = $result;
}

print json_encode(["last_page"=>1, "data" => $ds]);
?>
