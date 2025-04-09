<?php

use App\States\InstanceStatusState\Configuring;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description');
            $table->foreignUuid('created_by')->references('id')->on('users');

            // Virtual Machine settings received from API endpoint. Will be set later in the creation state
            $table->unsignedInteger('vm_id')->nullable();
            $table->string('vm_username')->nullable();
            $table->string('vm_password')->nullable();

            // State related information
            $table->boolean('is_ready')->default(false);
            $table->string('status')->default(Configuring::class);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('stopped_at')->nullable();
            $table->timestamp('suspended_at')->nullable();

            // Create polymorphic relationship
            $table->uuidMorphs('instanceable');

            $table->timestamps();
            $table->softDeletes();

            // Enforce that no instance can point to more than 1 server or container
            $table->unique('instanceable_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instances');
    }
};
