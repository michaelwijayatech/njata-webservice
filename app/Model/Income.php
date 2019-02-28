<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    //
    protected $table = 'Income';
    public $BASETABLE = "income";

    public $timestamps = false;

    public $NAME = "INCM";

    public $STATUS_INACTIVE = 0;
    public $STATUS_ACTIVE = 1;
}
