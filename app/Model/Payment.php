<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    //
    protected $table = 'Payment';
    public $BASETABLE = "payment";

    public $timestamps = false;

    public $NAME = "PAY";

    public $STATUS_INACTIVE = 0;
    public $STATUS_ACTIVE = 1;
}
