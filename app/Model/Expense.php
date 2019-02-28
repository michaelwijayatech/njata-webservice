<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    //
    protected $table = 'Expenses';
    public $BASETABLE = "expenses";

    public $timestamps = false;

    public $NAME = "EXPS";

    public $STATUS_INACTIVE = 0;
    public $STATUS_ACTIVE = 1;
}
