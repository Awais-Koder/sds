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
        Schema::create('submittels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_submittel_id')->nullable()->constrained('submittels')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->dateTime('submitted_time')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->dateTime('update_time')->nullable()->comment('Time of last update by the approver');
            $table->text('name')->nullable();
            $table->string('ref_no')->nullable();
            $table->boolean('new_submittel')->nullable();
            $table->boolean('re_submittel')->nullable();
            $table->boolean('additional_copies')->nullable();
            $table->boolean('soft_copy')->nullable();
            $table->dateTime('date')->nullable();
            $table->unsignedBigInteger('cycle')->nullable();
            $table->enum('status', ['submitted', 'approved', 'rejected'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submittels');
    }
};
