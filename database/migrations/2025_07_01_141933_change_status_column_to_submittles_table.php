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
        Schema::table('submittels', function (Blueprint $table) {
            $table->enum('status' , ['approved', 'approved_as_noted', 'revise_resubmit_as_noted', 'rejected', 'submitted' , 'draft'])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submittels', function (Blueprint $table) {
            $table->enum('status' , ['approved', 'approved_as_noted', 'revise_resubmit_as_noted', 'rejected', 'submitted'])->change();
        });
    }
};
