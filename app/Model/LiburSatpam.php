<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LiburSatpam extends Model
{
    //
    protected $table = 'LiburSatpam';
    public $BASETABLE = "libur_satpam";

    public $timestamps = false;

    public $NAME = "LBRS";

    public $STATUS_INACTIVE = 0;
    public $STATUS_ACTIVE = 1;
}
