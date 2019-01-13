<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    //
    protected $table = 'Contact';
    public $BASETABLE = "contact";

    public $timestamps = false;

    public $NAME = "CNT";

    public $STATUS_INACTIVE = 0;
    public $STATUS_ACTIVE = 1;
}
