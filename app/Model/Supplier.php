<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    //
    protected $table = 'Supplier';
    public $BASETABLE = "supplier";

    public $timestamps = false;

    public $NAME = "SUP";

    public $STATUS_INACTIVE = 0;
    public $STATUS_ACTIVE = 1;
}
