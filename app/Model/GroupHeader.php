<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class GroupHeader extends Model
{
    //
    protected $table = 'Group_header';
    public $BASETABLE = "group_header";

    public $timestamps = false;

    public $NAME = "GRPH";

    public $STATUS_INACTIVE = 0;
    public $STATUS_ACTIVE = 1;
}
