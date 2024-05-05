<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // Type of notification (e.g., 'user_registered', 'certificate_request')
            $table->string('title');
            $table->text('message'); // Notification message
            $table->string('link')->nullable();
            $table->json('data')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->boolean('read')->default(false); // Whether the notification has been read
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
