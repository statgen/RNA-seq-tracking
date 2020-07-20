#!/usr/bin/php

<?php

require_once("../lib/cli.inc.php");

if(!checkAdminPermission()) {
  exit;
}

//define source file path
define('SRC_FILE_DIR', getcwd() . '/src/');
$file_list = get_pending_files();

if(!$file_list->count()) {
  echo "No pending file has been found in the file_queue table! \n";
  exit;
} else {
  echo "Below are the file(s) that will be processed: \n";
  foreach ($file_list as $file) {
    echo $file->id . ". " . $file->filename . "\n";
  }
  echo "\nType 'yes' => continue, 'no' => exit; \n";
}

$handle = fopen ("php://stdin","r");
$line = fgets($handle);
if(trim($line) != 'yes'){
  echo "ABORTING!\n";
  exit;
}
echo "\nContinuing...\n";

foreach ($file_list as $file) {
  $query = "";
  if (file_exists(SRC_FILE_DIR . $file->filename)) {
    if(strpos(strtolower($file->filename), "init")) { //process samples data
      $target_table = "samples";
    } elseif (strpos(strtolower($file->filename), "metrics")) { // process qc metrics data
      $target_table = "qc_metrics";
    } else {
      echo "Error: file type is undefined! \n";
      exit;
    }
    $query = process_data_in_tsv(SRC_FILE_DIR.$file->filename, $target_table, $file->id);
    $conn = db_conn();
    if (mysqli_query($conn, $query) && set_processed($file->id)) {
      echo "New qc metrics updated successfully for ". $file["filename"] ."\n";
    } else {
      echo "Error: " . mysqli_error($conn) . "\n ";
    }
    $conn->close();
  } else {
    echo "Error, ".$file->filename." was not found in ".SRC_FILE_DIR."!\n";
    exit;
  }
}

/**
* mapping tsv header columns to table schema
* @param $type string 1. samples  2. metrics
* @return array
*/
function get_db_mapping_array($type) {
//mapping tsv file header columns to table schema
  $array = [];
  if($type === "samples") {
    $array = [
      "Sample" => "id",
      "Study" => "study_id",
      "Dataset" => "dataset_id",
      "TORID" => "torid",
      "Investigator_ID" => "investigator_id",
      "Family ID" => "family_id",
      "Good Faith Approved" => "good_faith_approved",
      "Tissue Type" => "tissue_type",
      "arrival_date" => "arrival_date"
    ];
  } elseif ($type==="qc_metrics") {
    foreach(QcAttributesMapper::all() as $mapper) {
      $array[$mapper->full_attribute] = $mapper->field_name;
    }

  } else {
    return null;
  }
  return $array;
}

/*
 * process data in tsv file and generate a set of insert statements
 * @param $file path + file name
 * @param $table table name , currently "samples" and "qc_metrics"
 * @param $qid file_queue_id
 * @return A set of insert query statement
 */
function process_data_in_tsv($file, $table, $qid=null) {
  if(!in_array($table, ["samples", "qc_metrics"])) {
    echo "Error: table name given is undefined in DB";
    exit;
  }
  $fields_db_mapping = get_db_mapping_array($table);
  $array = [];
  $inserts = [];
  $fields = [];
  $i = 0;
  //ODD: some files may contain empty last column
  $empty_last_column = false;
  if (($handle = fopen($file, "r")) !== FALSE) {
    while (($row = fgetcsv($handle, 2048, "\t")) !== FALSE) {
      if (empty($fields)) {
        $fields = $row;
        //remove the last column if empty, this happens in some files
        if(empty(end($fields))) {
          array_pop($fields);
          $empty_last_column = true;
        }
        for ($j=0; $j<count($fields); $j++) {
          $fields[$j] = trim($fields[$j]);
          if ($fields[$j] && array_key_exists($fields[$j], $fields_db_mapping)) {
            $fields[$j] = $fields_db_mapping[$fields[$j]];
          } else {
            echo "Error: empty header column found in tsv file, process terminated!\n";
            exit;
          }
        }
        $columns = implode("`,`", $fields);
        $file_queue_id = ($table=="qc_metrics")? ",`file_queue_id`":"";
        $sql = <<<SQL
INSERT INTO `rna_seq`.`{$table}` (`{$columns}`{$file_queue_id}) VALUES \n
SQL;
        continue;
      }
      //remove the last element if empty
      if($empty_last_column) {
        array_pop($row);
      }
      foreach ($row as $k=>$value) {
        $array[$i][$fields[$k]] = $value;
      }
      $file_queue_id_value = ($table == "qc_metrics")? ",\"" .$qid ."\"":"";
      $inserts[$i] = '("' . implode('","', $array[$i]) . '"' .$file_queue_id_value .')';
      $i++;
    }
    if (!feof($handle)) {
      echo "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
  }
  $sql .= implode(",\n", $inserts) . ";";
  return $sql;
}

/*
 * get pending files from the file_queue table
 * @return ORM object of files list
 *
 */
function get_pending_files() {
  $files = FileQueue::whereNull("processed_at")->get();
  return $files;
}

/*
 * set processed_at to now()
 * @param $id FileQueue id
 *
 */
function set_processed($id) {
  $file = FileQueue::find($id);
  $file->processed_at = date("Y-m-d H:i:s");
  $file->save();
  return true;
}
?>
