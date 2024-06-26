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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('admin_id');
            $table->string('name');
            $table->string('language');
            $table->string('code');
            $table->unsignedBigInteger('status');
            $table->unsignedBigInteger('approve1')->nullable();
            $table->unsignedBigInteger('approve2')->nullable();
            $table->unsignedBigInteger('approve3')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('admin_id')->references('id')->on('admins');
            $table->foreign('approve1')->references('id')->on('users');
            $table->foreign('approve2')->references('id')->on('users');
            $table->foreign('approve3')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
