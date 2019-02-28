<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    //
    protected $table = 'Balance';
    public $BASETABLE = "balance";

    public $timestamps = false;

    public $NAME = "BAL";

    public $STATUS_INACTIVE = 0;
    public $STATUS_ACTIVE = 1;
}
