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
            $table->integer('no_of_copies')->nullable()->after('cycle');
            $table->enum('file_location' , ['dc' , 'actioner' , 'viewer'])->nullable()->after('no_of_copies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outgoings', function (Blueprint $table) {
            $table->dropColumn('no_of_copies');
            $table->dropColumn('file_location');
        });
    }
};
