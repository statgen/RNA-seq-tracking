<?php

/*
 * This is for RNA-seq qc tracking
 * Loc: /var/www/rna_seq
 *
 */

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Connection as DB;

class FileQueue extends Eloquent {

  protected $connection = 'rnaseq';
  protected $table = 'file_queue';

  protected $primaryKey = 'id';

  public $timestamps = false;


 /*
 *    * Relationships
 *
 */

}

?>
