#!/usr/bin/php

<?php
require_once("../lib/cli.inc.php");

if(!checkAdminPermission()) {
  exit;
}

$helpmessage=<<<TEXT
\033[33mUsage:
  \033[0mcommand [options] [arguments]
\033[33mOptions:
  \033[0m-h, --help\t\tDisplay this help message
  -a, --add\t\tInsert a new user email into the whitelist
  -f, --find\t\tFind whether an email exists in the whilelist
  -r, --remove\t\tRemove an email from the whitelist
\033[33mExample commands:
  \033[0mphp user_whitelist.php --add {email}
  php user_whitelist.php --find {email}
  php user_whitelist.php -r {email}\n
TEXT;

//display help message
if(empty($argv[1]) || in_array($argv[1], ["--help", "-h", "-help"])) {
  echo $helpmessage;
  exit;
}

$email = "";

if(isset($argv[1])) {
  if(isset($argv[2])) {
    $email = $argv[2];
  } else {
    echo "Input email address: \n";
    $handle = fopen ("php://stdin","r");
    $email = trim(fgets($handle));
  }
  if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "\033[31mInvalid email format, please try again. \033[0m \n\n";
    exit;
  }
  if(in_array($argv[1], array('--add', '-add', '-a'))) {
    addWhitelist($email);
  } elseif (in_array($argv[1], array('--find', '-find', '-f'))) {
    findWhitelist($email);
  } elseif (in_array($argv[1], array('--remove', '-remove', '-r'))) {
    removeWhitelist($email);
  } else {
    echo "\033[31mInvalid option!\n\n";
    print $helpmessage;
    exit;
  }
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
  try {
    $user->save();
    echo $email ." has been added successfully\n";
  } catch (EXCEPTION $e) {
    echo "\033[31mError occurred, please try again! \033[0m\n\n";
  }
}

/*
 * @param $email user email
 *
 */
function findWhitelist($email) {
  if(userWhitelist::where("email", $email)->count()) {
    echo $email . " has already been in the white list\n";
  } else {
    echo $email ." not found\n";
  }
}

/*
 * @param $email user email
 * 
 */
function removeWhitelist($email) {
  while($user=userWhitelist::where("email", $email)->first()) {
    try {
      $user->delete();
    } catch (exception $e) {
      echo "\033[31mError occurred, please try again!\033[0m\n";
      exit;
    }
  }
  echo $email ." is no longer in the whitelist!\n";
}
?>
