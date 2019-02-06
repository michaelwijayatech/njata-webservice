<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Chop extends Model
{
    //
    protected $table = 'Chop';
    public $BASETABLE = "chop";

    public $timestamps = false;

    public $NAME = "CHOP";
    public $NUMBER_ONE = 1;
    public $NUMBER_THREE = 3;
    public $NUMBER_SINGAPORE = 2;

    public $STATUS_INACTIVE = 0;
    public $STATUS_ACTIVE = 1;
}
