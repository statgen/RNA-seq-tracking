<?php

require_once("/var/www/config.php");

function db_conn() {
  $conn = mysqli_connect(RNASEQ_DB_SERVER_NAME, RNASEQ_DB_USER_NAME, RNASEQ_DB_PASSWORD, RNASEQ_DB_NAME);
  return $conn;
}

function db_conn_nhlbi() {
  $conn = mysqli_connect(TOPMED_DB_SERVER_NAME, TOPMED_DB_USER_NAME, TOPMED_DB_PASSWORD, TOPMED_DB_NAME);
  return $conn;
}

function checkAdminPermission() {
  $username = posix_getpwuid(posix_geteuid())["name"];
  if(!in_array($username,unserialize(OMICS_ADMIN_USERS))){
    print "Admin permission denied!\n"; 
    return false;
  } else {
    return true;
  }
}
?>
