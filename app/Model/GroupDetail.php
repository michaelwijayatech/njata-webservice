<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GroupDetail extends Model
{
    //
    protected $table = 'Group_detail';
    public $BASETABLE = "group_detail";

    public $timestamps = false;

    public $NAME = "GRPD";

    public $STATUS_INACTIVE = 0;
    public $STATUS_ACTIVE = 1;
}
