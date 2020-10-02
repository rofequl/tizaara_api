<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->string('logo')->nullable()->default('upload/photo/brand.png');
            $table->string('admin_logo')->nullable()->default('upload/photo/brand.png');
            $table->string('admin_login_background')->nullable()->default('upload/photo/bg.jpg');
            $table->string('favicon')->nullable()->default('upload/photo/favicon.png');
            $table->string('site_name')->nullable()->default('Nemo');
            $table->string('address', 1000)->nullable()->default('Neptune');
            $table->string('phone', 100)->nullable()->default('01912345678');
            $table->string('email')->nullable()->default('admin@example.com');
            $table->string('facebook')->nullable()->default('admin@example.com');
            $table->string('instagram')->nullable()->default('https://www.instagram.com');
            $table->string('twitter')->nullable()->default('https://www.twitter.com');
            $table->string('youtube')->nullable()->default('https://www.youtube.com');
            $table->string('google_plus')->nullable()->default('https://www.googleplus.com');
            $table->string('footer_text')->nullable()->default('© 2020 Nemo');
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
        Schema::dropIfExists('general_settings');
    }
}
