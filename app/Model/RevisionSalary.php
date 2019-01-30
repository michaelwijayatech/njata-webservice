<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class RevisionSalary extends Model
{
    //
    protected $table = 'Revision_Salary';
    public $BASETABLE = "revision_salary";

    public $timestamps = false;

    public $NAME = "REV";

    public $STATUS_INACTIVE = 0;
    public $STATUS_ACTIVE = 1;
}
