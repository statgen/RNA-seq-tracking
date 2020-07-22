<?php
//start session as this dataset is protected
if(!isset($_SESSION)){
  session_start();
}

if(!$_SESSION['access_token'] OR !$_SESSION['omics_user']) {
  //Unauthorized
  http_response_code(401);
  exit;
}

require_once("/var/www/vendor/autoload.php");
require_once("/var/www/config.php");
require_once("/var/www/report/lib/database.php");

//ajax pagination params
$page = isset($_POST['page'])?$_POST['page']:1;
$size = isset($_POST['size'])?$_POST['size']:100;
$offset = ($page-1)*$size;

//customize what qc measures to display in qc_attributes_mapper table
$attributes=[2,4,12,18,19,20,24,66,70];
$columns = QcAttributesMapper::whereIn("id",$attributes)
  ->orderBy("field_name")
  ->pluck("field_name")
  ->toArray();
$columns[]="samples.torid";
$columns[]="samples.study_id";

//define base qc metrics query
$query = QcMetrics::join("samples","samples.id","=","sample_id")
  ->select($columns)
  ->where(function ($query) {
    //apply ajax study param if selected
    if($_POST['study']!="All studies" AND $_POST['study']) {
      $query->where("study_id", $_POST['study']);
    }
  })->where(function ($query) {
    //apply ajax filter param if exist
    if(isset($_POST['filters'])) {
      foreach($_POST['filters'] as $filter) {
        $query->where($filter["field"], $filter["type"], "%".$filter['value']."%");
      }
    }
  })->where(function ($query) {
    //apply ajax sortering params
    if(isset($_POST['sorters'])) {
      foreach($_POST['sorters'] as $sorter) {
        $query->orderBy($sorter["field"], $sorter["dir"]);
      }
    }
  });

//determine last_page for ajax pagination
$last_page = ceil($query->count()/$size);

$metrics = $query->limit($size)
  ->offset($offset)
  ->get()
  ->toArray();

print json_encode(["last_page"=>$last_page, "data" => $metrics]);

