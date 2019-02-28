<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableSalesDropAddColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('id_product');
            $table->dropColumn('quantity');
            $table->dropColumn('price');
            $table->string('paid')->after('total');
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
        Schema::table('sales', function (Blueprint $table) {
            $table->string('id_product')->after('id_distributor');
            $table->string('quantity')->after('date');
            $table->string('price')->after('quantity');
        });
    }
}
