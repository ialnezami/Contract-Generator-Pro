<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            // Workflow fields
            $table->string('status')->default('draft')->after('content');
            $table->timestamp('status_changed_at')->nullable()->after('status');
            $table->foreignId('status_changed_by')->nullable()->after('status_changed_at')->constrained('users')->onDelete('set null');
            
            // Approval fields
            $table->boolean('requires_approval')->default(false)->after('status_changed_by');
            $table->integer('approval_level')->default(1)->after('requires_approval');
            $table->timestamp('approved_at')->nullable()->after('approval_level');
            $table->foreignId('approved_by')->nullable()->after('approved_at')->constrained('users')->onDelete('set null');
            
            // Signature fields
            $table->boolean('is_signed')->default(false)->after('approved_by');
            $table->timestamp('signed_at')->nullable()->after('is_signed');
            $table->string('signed_by')->nullable()->after('signed_at');
            $table->string('signature_type')->default('digital')->after('signed_by');
            
            // Workflow timestamps
            $table->timestamp('activated_at')->nullable()->after('signature_type');
            $table->timestamp('completed_at')->nullable()->after('activated_at');
            $table->timestamp('terminated_at')->nullable()->after('completed_at');
            $table->timestamp('renewed_at')->nullable()->after('terminated_at');
            $table->foreignId('renewed_by')->nullable()->after('renewed_at')->constrained('users')->onDelete('set null');
            $table->foreignId('parent_contract_id')->nullable()->after('renewed_by')->constrained('contracts')->onDelete('set null');
            
            // Add indexes
            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'status']);
            $table->index(['expires_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropForeign(['status_changed_by']);
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['renewed_by']);
            $table->dropForeign(['parent_contract_id']);
            
            $table->dropColumn([
                'status',
                'status_changed_at',
                'status_changed_by',
                'requires_approval',
                'approval_level',
                'approved_at',
                'approved_by',
                'is_signed',
                'signed_at',
                'signed_by',
                'signature_type',
                'activated_at',
                'completed_at',
                'terminated_at',
                'renewed_at',
                'renewed_by',
                'parent_contract_id',
            ]);
        });
    }
};
