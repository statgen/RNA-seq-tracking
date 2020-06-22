#!/usr/bin/php

<?php
/*
 * option -f => filename
 *        -r +> qc source
 *        -d => qc date
*/

require_once("../lib/cli.inc.php");

if(!checkAdminPermission()) {
  exit;
}

echo $argv[0];
if(isset($argv[1]) && in_array($argv[1], array('--list', '-list'))) {
  listFiles();
  exit;
}

$opts = getopt("f:s:d:");
if(count($opts)<3 || in_array($argv[1], array('--help', '-help', '-h', '-?'))) {
  echo "3 argument(s) are required. See sample command below \n";
  echo "php add_filequeue.php -f \"filename\" -s \"qc source\" -d \"qc date(yyyy-mm-dd) \"\n\n";
  exit;
}

if(FileQueue::where("filename", $opts["f"])->count()) {
  echo "The file ".$opts["f"]. " already exists!\n";
  exit;
}

echo "Inserting ...\n";

$file = new FileQueue;
$file->filename = $opts["f"];
$file->qc_source = $opts["s"];
$file->qc_date = $opts["d"]." 00:00:00";

if ($file->save()) {
  echo "New record created successfully for " . $opts["f"] ."\n";
} else {
  echo "Error occurred!\n";
}

function listFiles() {
  $files = FileQueue::all();
  echo "filename\tqc_source\tqc_date\n";
  foreach($files as $file) {
    echo $file->filename."\t".$file->qc_source."\t".$file->qc_date."\n";
  }

}
?>
