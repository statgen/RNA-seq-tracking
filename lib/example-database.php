<?php

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

//TopMED The Whole Genome QC database, only the userWhitelist table is being used in OMICS project
$capsule->addConnection([
  'driver'    => 'mysql',
  'host'      => TOPMED_DB_SERVER_NAME,
  'database'  => TOPMED_DB_NAME,
  'username'  => TOPMED_DB_USER_NAME,
  'password'  => TOPMED_DB_PASSWORD,
  'charset'   => 'utf8',
  'collation' => 'utf8_unicode_ci',
  'prefix'    => '',
], 'nhlbi');

//TopMED OMICS database
$capsule->addConnection([
  'driver'    => 'mysql',
  'host'      => RNASEQ_DB_SERVER_NAME,
  'database'  => RNASEQ_DB_NAME,
  'username'  => RNASEQ_DB_USER_NAME,
  'password'  => RNASEQ_DB_PASSWORD,
  'charset'   => 'utf8',
  'collation' => 'utf8_unicode_ci',
  'prefix'    => '',
], 'rnaseq');

class RawQuery {

  protected $connection;
  public $result;

  public function __construct($database = '', $query = '', $inputs = []){
    global $capsule;
    if (!in_array($database, ['nhlbi', 'mapper'])){
      throw new Exception("Invalid database passed to RawQuery");
    }
    $this->connection = $capsule->getConnection($database);
    $this->result = $this->connection->select($query, $inputs);
  }

  public function get(){
    return $this->result;
  }

}
