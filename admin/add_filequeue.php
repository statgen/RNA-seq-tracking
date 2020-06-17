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

$opts = getopt("f:s:d:");

if(count($opts)<3 || in_array($argv[1], array('--help', '-help', '-h', '-?'))) {
  echo "3 argument(s) are required. See sample command below \n";
  echo "php load_srcqueue.php -f \"filename\" -r \"qc source\" -d \"qc date(yyyy-mm-dd) \"\n";
  exit;
}

echo "Inserting ...\n";
$opts["d"] = date("m/d/yy", strtotime($opts["d"]));
$opts = array_map('json_encode', $opts);
$query = <<<SQL
INSERT INTO `rna_seq`.`file_queue` (`filename`, `qc_source`, `qc_date`) VALUES
({$opts["f"]}, {$opts["s"]}, STR_TO_DATE({$opts["d"]}, '%m/%d/%Y %H:%i:%s'));
SQL;

$conn = db_conn();
if (mysqli_query($conn, $query)) {
  echo "New record created successfully for " . $opts["f"] ."\n";
} else {
  echo "Error: " . mysqli_error($conn) . "\n";
}
$conn->close();

?>
