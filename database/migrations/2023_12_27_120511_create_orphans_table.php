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
        Schema::create('orphans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('family_id');
            $table->dateTime('birthdate');
            $table->string('educationLevel');
            $table->string('class');
            $table->string('notes')->nullable();
            $table->string('insurance');
            $table->string('hasIllness');
            $table->string('illnesses')->nullable();
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();

            $table->foreign('family_id')->references('id')->on('families')->onDelete('cascade');
            $table->foreign('user_created')->references('id')->on('users');
            $table->foreign('user_updated')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orphans');
    }
};
