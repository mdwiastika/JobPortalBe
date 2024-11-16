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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_posting_id')->constrained('job_postings', 'id')->cascadeOnDelete();
            $table->foreignId('job_seeker_id')->constrained('users', 'id')->cascadeOnDelete();
            $table->text('cover_letter');
            $table->text('resume');
            $table->enum('status', ['applied', 'interview', 'hired', 'rejected']);
            $table->text('interview_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
