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
        Schema::create('contract_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->longText('content');
            $table->string('category');
            $table->boolean('is_public')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('version')->default('1.0');
            $table->json('tags')->nullable();
            $table->json('variables_schema')->nullable();
            $table->string('preview_image')->nullable();
            $table->integer('usage_count')->default(0);
            $table->decimal('rating', 2, 1)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_templates');
    }
};
