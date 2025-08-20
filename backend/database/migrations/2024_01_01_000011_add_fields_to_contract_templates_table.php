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
        Schema::table('contract_templates', function (Blueprint $table) {
            // Add category_id foreign key
            $table->foreignId('category_id')->nullable()->after('category')->constrained('template_categories')->onDelete('set null');
            
            // Add rating_count field
            $table->integer('rating_count')->default(0)->after('rating');
            
            // Add type field for template classification
            $table->string('type')->default('general')->after('category_id');
            
            // Add price field for premium templates
            $table->decimal('price', 8, 2)->nullable()->after('type');
            
            // Add approval status fields
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('is_active');
            $table->text('rejection_reason')->nullable()->after('approval_status');
            $table->foreignId('approved_by')->nullable()->after('rejection_reason')->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            
            // Add template versioning fields
            $table->string('parent_version_id')->nullable()->after('version');
            $table->boolean('is_latest_version')->default(true)->after('parent_version_id');
            
            // Add template statistics fields
            $table->integer('download_count')->default(0)->after('usage_count');
            $table->integer('favorite_count')->default(0)->after('download_count');
            
            // Drop the old category column since we now have category_id
            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contract_templates', function (Blueprint $table) {
            // Recreate the old category column
            $table->string('category')->after('content');
            
            // Drop all the new fields
            $table->dropForeign(['category_id']);
            $table->dropColumn([
                'category_id',
                'rating_count',
                'type',
                'price',
                'approval_status',
                'rejection_reason',
                'approved_by',
                'approved_at',
                'parent_version_id',
                'is_latest_version',
                'download_count',
                'favorite_count',
            ]);
        });
    }
};
