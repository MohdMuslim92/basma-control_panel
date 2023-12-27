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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            // The office he belongs to
            $table->unsignedBigInteger('office');
            // Admin is 0 means he is a member at the office, otherwise he maybe an admin or co-admin
            $table->unsignedBigInteger('admin');
            $table->dateTime('hired_at')->default(now()); // Automatically set the hired_at date
            $table->dateTime('ended_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
