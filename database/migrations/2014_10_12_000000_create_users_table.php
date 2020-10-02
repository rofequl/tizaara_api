<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('account_type')->nullable();
            $table->bigInteger('parent_id')->default(0);
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('photo_type')->default(0);
            $table->string('photo')->default('upload/photo/user.png');
            $table->string('username')->unique();
            $table->string('email')->unique()->nullable();
            $table->string('mobile')->unique()->nullable();
            $table->string('password');
            $table->string('verificationToken')->nullable();
            $table->string('is_verified')->default(0);
            $table->string('status')->default(0);
            $table->string('registration_type')->default(0);
            $table->string('ip_address')->nullable();
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
        Schema::dropIfExists('users');
    }
}
