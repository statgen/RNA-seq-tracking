#!/usr/bin/php

<?php

require_once("../lib/cli.inc.php");

//validate user permission
if(!checkAdminPermission()) {
  exit;
}

$helpmessage=<<<TEXT
\033[33mUsage:
  \033[0mcommand [options] [arguments]
\033[33mOptions:
  \033[0m-h, --help\t\tDisplay this help message
  -l, --list\t\tList available files in the file queue
  -a, --add\t\tInsert a new file into the file queue table
  -d, --delete\t\tRomove a existing file from the file queue table
\033[33mExample commands:
  \033[0mphp filequeue.php --add "filename" "qc source" "yyyy-mm-dd(date)"
  php filequeue.php --delete "filename"
  php filequeue.php -l\n
TEXT;

//display help message
if(empty($argv[1]) || in_array($argv[1], ["--help", "-h", "-help"])) {
  echo $helpmessage;
  exit;
}

//List available files in the file queue table
if(isset($argv[1]) && in_array($argv[1], array('--list', '-list', '-l'))) {
  listFiles();
  exit;
}

//Insert a new file into the file queue
if(isset($argv[1]) && in_array($argv[1], array('--add', '-add', '-a'))) {
  if(isset($argv[2]) && FileQueue::where("filename", $argv[2])->count()) {
    echo "\033[31mThe file '".$argv[2]. "' already exists!\033[0m \n";
    exit;
  } elseif (!isset($argv[2]) || !isset($argv[3]) || !isset($argv[4])) {
    echo "\033[31mMissing arguments ..., Use --help to check example commands! \033[0m\n";
    exit;
  } else {
    $file = new FileQueue;
    $file->filename = $argv[2];
    $file->qc_source = $argv[3];
    $file->qc_date = $argv[4]." 00:00:00";

    try {
      $file->save();
      echo "New record created successfully for the '" . $argv[2] ."'! \n";
      exit;
    } catch (Exception $e) {
      echo "\033[31mError occurred, please try again! \033[0m\n";
      exit;
    }
  }
}

//Remove a file
if(isset($argv[1]) && in_array($argv[1], array('--delete', '--del', '-d'))) {
  if(isset($argv[2]) && FileQueue::where("filename",$argv[2])->count()) {
    removeFile(FileQueue::where("filename", $argv[2])->first());
    exit;
  } else {
    echo "\033[31mMissing file name or file does not exist, please try again! \033[0m\n";
    exit;
  }
}

print $helpmessage;
exit;

function listFiles() {
  $files = FileQueue::all();
  echo "filename\tqc_source\tqc_date\n";
  foreach($files as $file) {
    echo $file->filename."\t".$file->qc_source."\t".$file->qc_date."\n";
  }
}

function removeFile($file) {
  try {
    $file->delete();
    echo "The file has been successfully removed!\n";
  } catch (Exception $e) {
    echo "\033[31mError occurred! \033[0m\n";
  }
}
?>
