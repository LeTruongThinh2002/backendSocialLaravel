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
        // Create 'reels' table
        Schema::create('reels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->string('description')->nullable();
            $table->string('media');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        // Create 'reels_like' table
        Schema::create('reels_like', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('reels_id')->unsigned();
            $table->timestamps();

            $table->primary(['user_id', 'reels_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('reels_id')->references('id')->on('reels')->onDelete('cascade');
        });
        // Create 'reels_comment' table
        Schema::create('reels_comment', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('reels_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('parent_comment_id')->unsigned()->nullable();
            $table->string('comment');
            $table->timestamps();

            $table->foreign('reels_id')->references('id')->on('reels')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('parent_comment_id')->references('id')->on('reels_comment')->onDelete('cascade');
        });

        // Create 'reels_comment_like' table
        Schema::create('reels_comment_like', function (Blueprint $table) {
            $table->bigInteger('comment_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->timestamps();

            $table->primary(['comment_id', 'user_id']);
            $table->foreign('comment_id')->references('id')->on('reels_comment')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reels');
        Schema::dropIfExists('reels_like');
        Schema::dropIfExists('reels_comment');
        Schema::dropIfExists('reels_comment_like');
    }
};
