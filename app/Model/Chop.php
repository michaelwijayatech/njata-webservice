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

    public $STATUS_INACTIVE = 0;
    public $STATUS_ACTIVE = 1;
}
