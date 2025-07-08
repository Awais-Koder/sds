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
            $table->string('soft_copy_file')->nullable();
            $table->boolean('sent_to_actioner')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submittels', function (Blueprint $table) {
            $table->dropColumn('soft_copy_file');
            $table->dropColumn('sent_to_actioner');
        });
    }
};
