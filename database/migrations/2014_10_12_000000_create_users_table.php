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
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('licao_1_1')->nullable();
            $table->string('licao_1_2')->nullable();
            $table->string('licao_1_3')->nullable();
            $table->string('licao_1_4')->nullable();
            $table->string('licao_1_5')->nullable();
            $table->string('licao_2_1')->nullable();
            $table->string('licao_2_2')->nullable();
            $table->string('licao_2_3')->nullable();
            $table->string('licao_2_4')->nullable();
            $table->string('licao_2_5')->nullable();
            $table->string('licao_3_1')->nullable();
            $table->string('licao_3_2')->nullable();
            $table->string('licao_3_3')->nullable();
            $table->string('licao_3_4')->nullable();
            $table->string('licao_3_5')->nullable();

            $table->rememberToken();
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
