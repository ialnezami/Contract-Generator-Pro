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
        Schema::create('template_variables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('template_id')->constrained('contract_templates')->onDelete('cascade');
            $table->string('name');
            $table->string('type'); // text, number, date, select, textarea
            $table->string('label');
            $table->boolean('required')->default(false);
            $table->text('default_value')->nullable();
            $table->json('options')->nullable(); // For select type
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('template_variables');
    }
};
