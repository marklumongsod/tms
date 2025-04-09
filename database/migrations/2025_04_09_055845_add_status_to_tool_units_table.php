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
        Schema::table('tool_units', function (Blueprint $table) {
            $table->enum('status', ['available', 'borrowed', 'returned'])->default('available');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tool_units', function (Blueprint $table) {
            $table->dropColumn('status'); 
        });
    }
};
