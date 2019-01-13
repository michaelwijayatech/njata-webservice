<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    //
    protected $table = 'Holiday';
    public $BASETABLE = "holiday";

    public $timestamps = false;

    public $NAME = "HOL";

    public $STATUS_INACTIVE = 0;
    public $STATUS_ACTIVE = 1;
}
