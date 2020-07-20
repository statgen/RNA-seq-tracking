<?php

/*
 * This is for RNA-seq qc tracking
 * Loc: /var/www/rna_seq
 *
 */

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Connection as DB;

class Sample extends Eloquent {

  protected $connection = 'rnaseq';
  protected $table = 'samples';

  protected $primaryKey = 'id';

  public $timestamps = false;


 /*
 *    * Relationships
 *
 */

}

?>
