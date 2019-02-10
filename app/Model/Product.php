<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $table = 'Product';
    public $BASETABLE = "product";

    public $timestamps = false;

    public $NAME = "PRD";

    public $STATUS_INACTIVE = 0;
    public $STATUS_ACTIVE = 1;
}
