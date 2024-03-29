<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableServiceSaleColumnClientId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_sale', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->after('is_prolongation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_sale', function (Blueprint $table) {
            $table->dropColumn('client_id');
        });
    }
}
