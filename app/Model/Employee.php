<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'Employee';
    public $BASETABLE = "employee";

    public $timestamps = false;

    public $NAME = "EMPLO";
    public $GENDER_MALE = 1;
    public $GENDER_FEMALE = 2;

    public $STATUS_HARIAN_ATAS = 1;
    public $STATUS_HARIAN_BAWAH = 3;
    public $STATUS_BORONGAN = 2;

    public $STATUS_INACTIVE = 0;
    public $STATUS_ACTIVE = 1;
}
