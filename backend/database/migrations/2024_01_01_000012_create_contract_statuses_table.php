<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contract_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained()->onDelete('cascade');
            $table->string('status');
            $table->string('previous_status')->nullable();
            $table->foreignId('changed_by')->constrained('users')->onDelete('cascade');
            $table->text('reason')->nullable();
            $table->timestamp('changed_at');
            $table->timestamps();
            
            $table->index(['contract_id', 'changed_at']);
            $table->index(['status', 'changed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_statuses');
    }
};
