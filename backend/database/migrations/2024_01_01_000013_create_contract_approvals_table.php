<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->foreignId('approver_id')->constrained('users')->onDelete('cascade');
            $table->integer('level')->default(1);
            $table->boolean('required')->default(true);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('requested_at');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('due_date')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
            
            $table->unique(['contract_id', 'approver_id']);
            $table->index(['contract_id', 'level']);
            $table->index(['status', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_approvals');
    }
};
