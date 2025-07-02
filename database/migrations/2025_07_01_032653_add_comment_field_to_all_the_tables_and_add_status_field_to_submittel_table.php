<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('submittels', function (Blueprint $table) {
            $table->enum('status', ['approved', 'approved_as_noted', 'revise_resubmit_as_noted', 'rejected', 'submitted'])->nullable()->change();
        });

        foreach (['submittels', 'outgoings', 'incomings'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->text('comments')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submittels', function (Blueprint $table) {
            $table->enum('status', ['submitted', 'approved', 'rejected'])->nullable()->change();
        });

        foreach (['submittels', 'outgoings', 'incomings'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('comments')->change();
            });
        }
    }
};
