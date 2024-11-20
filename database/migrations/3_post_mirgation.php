<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->timestamps();

            // Thêm cột description với kiểu json tạm thời
            $table->json('description')->nullable();

            // Tạo khóa ngoại cho user_id
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Đổi cột description thành jsonb
        DB::statement('ALTER TABLE posts ALTER COLUMN description TYPE JSONB USING description::JSONB');

        // Create 'posts_media' table
        Schema::create('posts_media', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('post_id')->unsigned();
            $table->string('media');
            $table->timestamps();

            $table->primary(['id', 'post_id']);
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });
        // Create 'posts_comment' table
        Schema::create('posts_comment', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('parent_comment_id')->unsigned()->nullable();
            $table->bigInteger('post_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->string('comment');
            $table->timestamps();

            $table->foreign('parent_comment_id')->references('id')->on('posts_comment')->onDelete('cascade');
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        // Create 'posts_comment_like' table
        Schema::create('posts_comment_like', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('comment_id')->unsigned();
            $table->timestamps();

            $table->primary(['user_id', 'comment_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('comment_id')->references('id')->on('posts_comment')->onDelete('cascade');
        });

        // Create 'posts_like' table
        Schema::create('posts_like', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('post_id')->unsigned();
            $table->timestamps();

            $table->primary(['user_id', 'post_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
        Schema::dropIfExists('posts_media');
        Schema::dropIfExists('posts_comment');
        Schema::dropIfExists('posts_like');
        Schema::dropIfExists('posts_comment_like');
    }
};
