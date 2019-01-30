<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableEmployeeAddPotonganBpjs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('employee', function (Blueprint $table) {
            $table->string('potongan_bpjs')->after('premi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('employee', function (Blueprint $table) {
            $table->dropColumn('potongan_bpjs');
        });
    }
}
