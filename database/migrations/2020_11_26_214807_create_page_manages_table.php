<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePageManagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_manages', function (Blueprint $table) {
            $table->id();
            $table->longText('terms_condition')->nullable();
            $table->date('terms_update')->nullable();
            $table->longText('privacy')->nullable();
            $table->date('privacy_update')->nullable();
            $table->longText('about_us')->nullable();
            $table->longText('join_sales')->nullable();
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
        Schema::dropIfExists('page_manages');
    }
}
