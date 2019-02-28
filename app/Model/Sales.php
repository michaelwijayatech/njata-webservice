<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    //
    protected $table = 'Sales';
    public $BASETABLE = "sales";

    public $timestamps = false;

    public $NAME = "SELH";

    public $STATUS_INACTIVE = 0;
    public $STATUS_ACTIVE = 1;
    public $STATUS_PAID = 2;
}
