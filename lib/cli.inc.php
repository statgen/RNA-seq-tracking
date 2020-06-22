<?php

require_once("/var/www/vendor/autoload.php");
require_once("/var/www/config.php");
require_once("/var/www/report/lib/database.php");

function db_conn() {
  $conn = mysqli_connect(RNASEQ_DB_SERVER_NAME, RNASEQ_DB_USER_NAME, RNASEQ_DB_PASSWORD, RNASEQ_DB_NAME);
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
