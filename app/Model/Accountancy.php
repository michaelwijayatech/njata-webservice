<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Accountancy extends Model
{
    //
    protected $table = 'Accountancy';
    public $BASETABLE = "accountancy";

    public $timestamps = false;

    public $NAME = "ACC";

    public $STATUS_INACTIVE = 0;
    public $STATUS_ACTIVE = 1;
}
