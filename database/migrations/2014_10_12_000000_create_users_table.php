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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('gender');
            $table->unsignedBigInteger('state_id');
            $table->unsignedBigInteger('province_id');
            $table->string('address');
            $table->string('phone');
            $table->string('dob');
            $table->string('educationLevel');
            $table->string('specialization')->nullable();
            $table->string('skills');
            $table->string('alreadyVolunteering');
            $table->string('organizationName')->nullable();
            $table->string('volunteeringStartDate')->nullable();
            $table->string('volunteeringEndDate')->nullable();
            $table->string('monthlyShare');
            $table->string('meetingDay');
            $table->unsignedBigInteger('role_id')->nullable();
            $table->string('terms');
            $table->rememberToken();
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            // Status: 0->inactivated, 1->active, 2->deleted...etc
            $table->unsignedBigInteger('status')->default(0);
            $table->string('admin_mail')->nullable();
            $table->timestamp('last_pay')->nullable();
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('roles');
            $table->foreign('state_id')->references('id')->on('states');
            $table->foreign('province_id')->references('id')->on('provinces');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
