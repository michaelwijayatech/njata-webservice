<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Haid extends Model
{
    //
    protected $table = 'Haid';
    public $BASETABLE = "haid";

    public $timestamps = false;

    public $NAME = "HAID";

    public $STATUS_INACTIVE = 0;
    public $STATUS_ACTIVE = 1;
}
