<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
    //
    protected $table = 'Distributor';
    public $BASETABLE = "distributor";

    public $timestamps = false;

    public $NAME = "DIS";

    public $STATUS_INACTIVE = 0;
    public $STATUS_ACTIVE = 1;
}
