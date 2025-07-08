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
        Schema::create('submittel_final_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submittel_id')->constrained('submittels')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('ref_no')->nullable();
            $table->string('file')->nullable();
            $table->string('cycle')->nullable();
            $table->enum('status', ['approved', 'under_review', 'approved_as_noted', 'revise_and_resubmit', 'rejected'])->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submittel_final_reports');
    }
};
