<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTypeColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('message_logs', function (Blueprint $table) {
            $table->text('message')->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('url', 3000)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('message_logs', function($table)
        {
            $table->string('message')->change();
        });

        Schema::table('products', function($table)
        {
            $table->string('url')->change();
        });
    }
}
