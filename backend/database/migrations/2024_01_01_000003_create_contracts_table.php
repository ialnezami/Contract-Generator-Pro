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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('content');
            $table->enum('status', ['draft', 'active', 'expired', 'cancelled'])->default('draft');
            $table->foreignId('template_id')->constrained('contract_templates')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->decimal('total_value', 15, 2)->nullable();
            $table->string('currency', 3)->default('USD');
            $table->boolean('is_signed')->default(false);
            $table->timestamp('signed_at')->nullable();
            $table->string('signed_by')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
