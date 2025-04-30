<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('port_numbers', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn('id');

            $table->uuid('id')->primary();
        });
    }

    public function down(): void
    {
        Schema::table('port_numbers', function (Blueprint $table) {
            $table->dropPrimary();
            $table->dropColumn('id');

            $table->id()->change();
        });
    }
};
