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
        Schema::create('families', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('mother');
            $table->dateTime('mother_birthdate');
            $table->unsignedBigInteger('state_id');
            $table->unsignedBigInteger('province_id');
            $table->string('address');
            $table->string('phone');
            $table->string('otherPhone')->nullable();
            $table->string('sponsor');
            $table->string('educationLevel');
            $table->string('skills');
            $table->string('isSheWorking');
            $table->string('workDetails')->nullable();
            $table->string('accommodationType');
            $table->string('rentingAmount')->nullable();
            $table->string('gpsLocation')->nullable();
            $table->string('project')->nullable();
            $table->string('deserveSupport');
            $table->string('supportType');
            $table->string('support')->nullable();
            $table->string('hasIllness');
            $table->string('illnesses')->nullable();
            $table->string('notes')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->unsignedBigInteger('user_created');
            $table->unsignedBigInteger('user_updated')->nullable();
            $table->timestamps();

            $table->foreign('state_id')->references('id')->on('states');
            $table->foreign('province_id')->references('id')->on('provinces');
            $table->foreign('user_created')->references('id')->on('users');
            $table->foreign('user_updated')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('families');
    }
};
