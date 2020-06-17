<?php
if(!isset($_SESSION)){
  session_start();
}
require_once($_SERVER['DOCUMENT_ROOT'] . '/omics/lib/database.php');

if(!$_SESSION['access_token'] OR !$_SESSION['omics_user']) {
  //Unauthorized
  http_response_code(401);
  exit;
}


//ajax pagination params
$page = isset($_POST['page'])?$_POST['page']:1;
$size = isset($_POST['size'])?$_POST['size']:100;

//ajax filtering params
$whereFilter = "where 1";
if(isset($_POST['filters'])) {
  foreach($_POST['filters'] as $filter) {
    $value = (strtolower($filter["type"])==="like")?"%".$filter['value']."%":$filter['value'];
    $whereFilter .= " and ".$filter['field']." ".$filter['type']." '".$value."' ";
  }
}

//ajax sortering params
$orderBy = "";
$sorterArray = [];
if(isset($_POST['sorters'])) {
  $orderBy = " ORDER BY "; 
  foreach($_POST['sorters'] as $sorter) {
     $sorterArray[] = $sorter["field"] . " " . $sorter["dir"];
  }
  $orderBy .= implode($sorterArray);
}

$offset = ($page-1)*$size;
$whereStudy  = ($_POST['study']!="all" AND $_POST['study'])?'AND study_id ='.json_encode($_POST['study']):'';

//customize what qc measures to display
$sql = <<<SQL
SELECT GROUP_CONCAT(field_name)as columns from qc_attributes_mapper where weight = 1 and id in (2,3,6,13,14,18,23,24,59,61) order by id ASC;
SQL;

$query = new RawQuery('rnaseq', $sql);
$result = $query->get(); 

//determine last_page for ajax pagination
$sqlRows = <<<SQL
SELECT COUNT(1) as count FROM samples 
{$whereFilter} {$whereStudy};
SQL;
error_log($sqlRows);
$countQuery = new RawQuery('rnaseq', $sqlRows);
$rows = $countQuery->get();
$last_page = ceil($rows[0]->count/$size);

$qcSql = <<<SQL
SELECT samples.id, samples.study_id, {$result[0]->columns} 
  FROM samples join qc_metrics 
  on samples.id = qc_metrics.sample_id
  {$whereFilter} {$whereStudy}
  {$orderBy}   
  LIMIT {$size}
  OFFSET {$offset};
SQL;
error_log($qcSql);
$qcQuery = new RawQuery('rnaseq', $qcSql);
$metrics = $qcQuery->get(); 

$ds=[];
foreach($metrics as $row) {
  $ds[] = $row;
}

print json_encode(["last_page"=>$last_page, "data" => $ds]);

