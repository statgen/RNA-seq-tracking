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
  addWhitelist($email);
} else {
  print "Invalid email, aborted ...\n";
  exit;
}

/*
 * @param $email user email
 *
 */
function addWhitelist($email) {
  if(userWhitelist::where("email", $email)->count()) {
    echo $email . " has already been in the white list, aborted ...\n";
    exit;
  }
  $user = new userWhitelist;
  $user->email = $email;
  if ($user->save()) {
    echo $email ." has been added successfully\n";
  } else {
    echo "Error occurred, please try again\n";
  }
}
?>
