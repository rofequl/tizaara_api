<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompanyBasicInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_basic_infos', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->boolean('address_type')->nullable();
            $table->integer('reg_address_id')->nullable();
            $table->integer('ope_address_id')->nullable();
            $table->string('name', 150);
            $table->string('display_name', 150)->nullable();
            $table->date('establishment_date')->nullable();
            $table->string('office_space')->nullable();
            $table->string('website', 150)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('cell', 50)->nullable();
            $table->string('fax', 50)->nullable();
            $table->boolean('number_of_employee')->nullable();
            $table->string('ownership_type', 100)->nullable();
            $table->boolean('revenue')->nullable();
            $table->string('main_product')->nullable();
            $table->string('other_product')->nullable();
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
        Schema::dropIfExists('company_basic_infos');
    }
}
