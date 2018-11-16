<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdministratorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('administrator', function (Blueprint $table) {
            $table->string('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('user_name');
            $table->string('password');
            $table->string('email');
            $table->string('role');
            $table->string('dob');
            $table->smallInteger('gender');
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
        Schema::dropIfExists('administrator');
    }
}
