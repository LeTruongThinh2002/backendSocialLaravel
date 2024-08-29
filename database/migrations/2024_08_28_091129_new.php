<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
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

        // Create 'news' table
        Schema::create('news', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('description');
            $table->string('media')->nullable();
            $table->timestamps();
            $table->bigInteger('user_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Create 'chats' table
        Schema::create('chats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('owner_id')->unsigned();
            $table->string('name');
            $table->string('background')->nullable();
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Create 'posts' table
        Schema::create('posts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('description');
            $table->timestamps();
            $table->bigInteger('user_id')->unsigned();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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

        // Create 'user_block' table
        Schema::create('user_block', function (Blueprint $table) {
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('user_blocked')->unsigned();
            $table->timestamps();

            $table->primary(['user_id', 'user_blocked']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_blocked')->references('id')->on('users')->onDelete('cascade');
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

        // Create 'messages' table
        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('chat_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->string('value')->nullable();
            $table->boolean('status')->default(false);
            $table->timestamps();

            $table->foreign('chat_id')->references('id')->on('chats')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Create 'messages_media' table
        Schema::create('messages_media', function (Blueprint $table) {
            $table->bigInteger('message_id')->unsigned();
            $table->string('media');

            $table->primary(['message_id', 'media']);
            $table->foreign('message_id')->references('id')->on('messages')->onDelete('cascade');
        });

        // Create 'posts_media' table
        Schema::create('posts_media', function (Blueprint $table) {
            $table->bigInteger('post_id')->unsigned();
            $table->string('media');

            $table->primary(['post_id', 'media']);
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
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

        // Create 'chats_member' table
        Schema::create('chats_member', function (Blueprint $table) {
            $table->bigInteger('chat_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->timestamps();

            $table->primary(['chat_id', 'user_id']);
            $table->foreign('chat_id')->references('id')->on('chats')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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

    public function down(): void
    {
        Schema::dropIfExists('reels_comment_like');
        Schema::dropIfExists('chats_member');
        Schema::dropIfExists('reels_comment');
        Schema::dropIfExists('posts_media');
        Schema::dropIfExists('messages_media');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('posts_like');
        Schema::dropIfExists('posts_comment_like');
        Schema::dropIfExists('reels_like');
        Schema::dropIfExists('user_block');
        Schema::dropIfExists('posts_comment');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('chats');
        Schema::dropIfExists('news');
        Schema::dropIfExists('reels');
    }
};
