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
        Schema::table('outgoings', function (Blueprint $table) {
            $table->foreignId('submitted_by')->nullable()->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->dateTime('submitted_time')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->dateTime('update_time')->nullable()->comment('Time of last update by the approver');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outgoings', function (Blueprint $table) {
            $table->dropColumn('submitted_by');
            $table->dropColumn('submitted_time');
            $table->dropColumn('approved_by');
            $table->dropColumn('update_time');
        });
    }
};
