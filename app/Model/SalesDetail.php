<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class SalesDetail extends Model
{
    //
    protected $table = 'Sales_Detail';
    public $BASETABLE = "sales_detail";

    public $timestamps = false;

    public $NAME = "SELD";

    public $STATUS_INACTIVE = 0;
    public $STATUS_ACTIVE = 1;
}
