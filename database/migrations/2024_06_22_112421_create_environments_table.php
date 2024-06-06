<?php

use App\Models\Node;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('environments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedSmallInteger('vm_id');
            $table->foreignIdFor(Node::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class)->constrained('users');
            $table->tinyInteger('cores');
            $table->string('memory');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('environments');
    }
};
