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
        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recruiter_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->nullable();
            $table->text('description');
            $table->text('requirements');
            $table->enum('employment_type', ['full_time', 'part_time', 'internship', 'contract']);
            $table->enum('experience_level', ['beginner', 'medium', 'expert']);
            $table->enum('work_type', ['on_site', 'remote', 'hybrid']);
            $table->double('min_salary', 15, 2);
            $table->double('max_salary', 15, 2);
            $table->text('location');
            $table->boolean('is_disability')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_postings');
    }
};
