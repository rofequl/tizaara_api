<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePriceVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_variations', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->double('off_price',10,2)->nullable()->default(0.00);
            $table->integer('min_qty')->default(0);
            $table->integer('max_qty')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('price_variations');
    }
}
