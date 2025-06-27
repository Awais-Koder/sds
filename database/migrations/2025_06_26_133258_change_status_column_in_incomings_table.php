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
        Schema::table('incomings', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('incomings', function (Blueprint $table) {
            $table->enum('status', [
                'approved',
                'approved_as_noted',
                'revise_and_resubmit',
                'rejected',
                'under_review',
            ])->default('under_review')->nullable();
            $table->enum('file_location' , ['actioner' , 'viewer'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incomings', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('file_location');
        });
        Schema::table('incomings', function (Blueprint $table) {
            $table->enum('status', [
                'approved',
                'approved_as_noted',
                'revise_and_resubmit',
                'rejected',
            ])->nullable();
        });
    }
};
