<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Carton extends Model
{
    //
    protected $table = 'Carton';
    public $BASETABLE = "carton";

    public $timestamps = false;

    public $NAME = "CRTN";

    public $STATUS_INACTIVE = 0;
    public $STATUS_ACTIVE = 1;
}
