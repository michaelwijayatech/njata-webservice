<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Administrator extends Model
{
    protected $table = 'Administrator';
    public $BASETABLE = "administrator";

    public $timestamps = false;

    public $NAME = "ADM";
    public $GENDER_MALE = 1;
    public $GENDER_FEMALE = 2;

    public $STATUS_INACTIVE = 0;
    public $STATUS_ACTIVE = 1;
//    public $BASEPATH = "Backend.administrator.";
//    public $BASEROUTE = "/admin/administrator/";
}
