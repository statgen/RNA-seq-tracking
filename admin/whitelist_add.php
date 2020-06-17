#!/usr/bin/php

<?php
require_once("../lib/cli.inc.php");

if(!checkAdminPermission()) {
  exit;
}

$email = "";

if(count($argv)<2 || in_array($argv[1], array('--help', '-help', '-h', '-?'))) {
  echo "Input email address: \n";
  $handle = fopen ("php://stdin","r");
  $email = trim(fgets($handle));
} else {
  $email = $argv[1];
}

if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
  addWhitelist(json_encode($email));
} else {
  print "Invalid email, aborted ...\n";
  exit; 
}
  
function addWhitelist($email) {
  $select = <<<SQL
SELECT email from `nhlbi`.`user_whitelist` 
  WHERE `email` = {$email};
SQL;
  $query = <<<SQL
INSERT INTO `nhlbi`.`user_whitelist` (`email`) VALUES ({$email});
SQL;
  $conn = db_conn_nhlbi();
  $result=mysqli_query($conn, $select);
  if(mysqli_num_rows($result) > 0) {
    echo $email . " has already been in the white list, aborted ...\n";
    exit;
  }
  if (mysqli_query($conn, $query)) {
    echo $email ." has been added successfully\n";
  } else {
    echo "Error: " . mysqli_error($conn) . "\n";
  }
  $conn->close();
}
?>
