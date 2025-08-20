<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->foreignId('signer_id')->constrained('users')->onDelete('cascade');
            $table->enum('signature_type', ['digital', 'electronic', 'wet'])->default('digital');
            $table->timestamp('signed_at');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('signature_data')->nullable();
            $table->string('verification_hash')->nullable();
            $table->timestamps();
            
            $table->index(['contract_id', 'signed_at']);
            $table->index(['signer_id', 'signed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_signatures');
    }
};
