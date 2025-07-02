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
            $table->dateTime('send_by_dc_to_actioner')->nullable()->comment('sned from dc to actioner timestamp');
            $table->dateTime('mark_by_actioner')->nullable()->comment('mark by actioner timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submittels', function (Blueprint $table) {
            $table->dropColumn('send_by_dc_to_actioner');
            $table->dropColumn('mark_by_actioner');
        });
    }
};
