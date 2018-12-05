<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee', function (Blueprint $table) {
            $table->string('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone_1')->nullable();
            $table->string('phone_2')->nullable();
            $table->string('domicile_address')->nullable();
            $table->string('premi')->nullable();
            $table->string('dob');
            $table->string('start_date');
            $table->string('end_date')->nullable();
            $table->string('image_ktp')->nullable();
            $table->string('image_kk')->nullable();
            $table->string('image_bpjs_ketenagakerjaan')->nullable();
            $table->string('image_bpjs_kesehatan')->nullable();
            $table->smallInteger('gender');
            $table->smallInteger('status');
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
        Schema::dropIfExists('employee');
    }
}
