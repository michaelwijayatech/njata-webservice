<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    //
    protected $table = 'Account';
    public $BASETABLE = "account";

    public $timestamps = false;

    public $NAME = "ACNT";

    public $STATUS_INACTIVE = 0;
    public $STATUS_ACTIVE = 1;
}
