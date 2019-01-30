<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableRevisionSalary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('revision_salary', function (Blueprint $table) {
            $table->string('msit')->after('date');
            $table->string('pokok')->after('msit');
            $table->string('premi')->after('pokok');
            $table->string('haid')->after('premi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('revision_salary', function (Blueprint $table) {
            $table->dropColumn('msit');
            $table->dropColumn('pokok');
            $table->dropColumn('premi');
            $table->dropColumn('haid');
        });
    }
}
