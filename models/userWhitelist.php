<?php

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Connection as DB;

class userWhitelist extends Eloquent {

  protected $connection = 'nhlbi';
  protected $table = 'user_whitelist';

  protected $primaryKey = 'id';

  protected $fillable = ['name',
                         'last_login'
                        ];

  public $timestamps = false;


 /*
 *    * Relationships
 *
 */


}

?>
