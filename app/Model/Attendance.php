<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    //
    protected $table = 'Attendance';
    public $BASETABLE = "attendance";

    public $timestamps = false;

    public $NAME = "ATT";

    public $STATUS_TIDAK_MASUK = 0;
    public $STATUS_MASUK = 1;
    public $STATUS_SETENGAH_HARI = 2;
    public $STATUS_IJIN = 3;
    public $STATUS_CUTI = 4;

    public $STATUS_INACTIVE = 0;
    public $STATUS_ACTIVE = 1;
}
