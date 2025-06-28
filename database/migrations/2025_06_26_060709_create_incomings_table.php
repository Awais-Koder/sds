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
        Schema::create('incomings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('submittel_id')->constrained('submittels')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('file')->nullable();
            $table->string('sds_no')->nullable();
            $table->string('dwg_no')->nullable();
            $table->string('description')->nullable();
            $table->enum('status', ['approved', 'approved_as_noted', 'revise_and_resubmit', 'rejected'])->nullable();
            $table->unsignedBigInteger('cycle')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incomings');
    }
};
