<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Standard extends Model
{
    //
    protected $table = 'Standard';
    public $BASETABLE = "standard";

    public $timestamps = false;

    public $NAME = "STD";

    public $STATUS_INACTIVE = 0;
    public $STATUS_ACTIVE = 1;
}
