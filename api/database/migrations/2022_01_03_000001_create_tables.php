<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('markets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('url');
            $table->string('title');
            $table->smallInteger('status')->default(1);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('url');
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->smallInteger('status')->default(1);
            $table->bigInteger('market_id')->nullable();
            $table->bigInteger('external_id');

            $table->timestamps();

            $table->foreign('market_id')
                ->references('id')
                ->on('markets')
                ->onDelete('cascade');

            $table->index('market_id');
            $table->index('external_id');
            $table->index('url');

            $table->unique(['market_id', 'external_id'], 'U_market_id_external_id');
        });

        Schema::create('product_prices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('product_id');
            $table->unsignedDecimal('price', 12);
            $table->integer('percent');

            $table->timestamps();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');

        });

        Schema::create('subscribers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('telegram_id')->nullable();
            $table->text('first_name')->nullable();
            $table->text('last_name')->nullable();
            $table->text('username')->nullable();
            $table->smallInteger('status')->default(1);

            $table->timestamps();

            $table->index('telegram_id');
        });

        Schema::create('product_subscribers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('subscriber_id');
            $table->bigInteger('product_id');

            $table->timestamps();

            $table->unique(['subscriber_id', 'product_id'], 'U_subscriber_id_product_id');

            $table->foreign('subscriber_id')
                ->references('id')
                ->on('subscribers')
                ->onDelete('cascade');

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
        });

        Schema::create('message_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('subscriber_id');
            $table->string('message');
            $table->timestamps();

            $table->foreign('subscriber_id')
                ->references('id')
                ->on('subscribers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message_logs');
        Schema::dropIfExists('product_subscribers');
        Schema::dropIfExists('subscribers');
        Schema::dropIfExists('product_prices');
        Schema::dropIfExists('products');
        Schema::dropIfExists('markets');
    }
}
