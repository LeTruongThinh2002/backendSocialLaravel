<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Create 'chats' table
        Schema::create('chats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('owner_id')->unsigned();
            $table->string('name');
            $table->string('background')->nullable();
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
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

    }

    public function down(): void
    {
        Schema::dropIfExists('chats');
        Schema::dropIfExists('chats_member');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('messages_media');
    }
};
