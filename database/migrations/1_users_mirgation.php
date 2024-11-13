<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->date('date_of_birth')->default(now());
            $table->string('country');
            $table->string('avatar')->nullable();
            $table->string('background')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('remember_token')->nullable();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('user_follow', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('user_following')->unsigned();
            $table->timestamps();

            $table->primary(['user_id', 'user_following']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_following')->references('id')->on('users')->onDelete('cascade');
        });

        // Create 'user_block' table
        Schema::create('user_block', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('user_blocked')->unsigned();
            $table->timestamps();

            $table->primary(['user_id', 'user_blocked']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_blocked')->references('id')->on('users')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('user_follow');
        Schema::dropIfExists('user_block');

    }
};
