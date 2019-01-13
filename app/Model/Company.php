<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'Company';
    public $BASETABLE = "company";

    public $timestamps = false;

    public $NAME = "CMP";

    public $STATUS_INACTIVE = 0;
    public $STATUS_ACTIVE = 1;
}
