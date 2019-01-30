<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRevisionSalary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('revision_salary', function (Blueprint $table) {
            $table->string('id');
            $table->string('id_employee');
            $table->string('date');
            $table->string('potongan_bpjs');
            $table->string('total_before');
            $table->string('total_revisi');
            $table->string('total_after');
            $table->smallInteger('is_active');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('revision_salary');
    }
}
