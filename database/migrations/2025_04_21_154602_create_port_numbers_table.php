<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('port_numbers', function (Blueprint $table) {
            $table->id();
            $table->integer('port');
            $table->boolean('is_active');
            $table->foreignUuid('container_id')->references('id')->on('containers')->onDelete('cascade');
            $table->foreignUuid('allocated_on')->references('id')->on('servers')->onDelete('cascade');
            $table->unique(['port', 'allocated_on'], 'unique_ports_per_server');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('port_numbers');
    }
};
