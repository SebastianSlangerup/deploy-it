<?php

use App\Models\Dependency;
use App\Models\Template;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dependencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('command');
            $table->timestamps();
        });

        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('dependency_template', function (Blueprint $table) {
            $table->foreignIdFor(Dependency::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Template::class)->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dependency_template');
    }
};
