<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    //
    protected $table = 'Purchase';
    public $BASETABLE = "purchase";

    public $timestamps = false;

    public $NAME = "PCS";

    public $STATUS_INACTIVE = 0;
    public $STATUS_ACTIVE = 1;
}
