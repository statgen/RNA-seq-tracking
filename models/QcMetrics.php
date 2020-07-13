<?php

/*
 * This is for RNA-seq qc tracking
 * Loc: /var/www/rna_seq
 *
 */

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Connection as DB;

class QcMetrics extends Eloquent {

  protected $connection = 'rnaseq';
  protected $table = 'qc_metrics';

  protected $primaryKey = 'sample_id';

  public $timestamps = false;


 /*
 *    * Relationships
 *
 */

}

?>
