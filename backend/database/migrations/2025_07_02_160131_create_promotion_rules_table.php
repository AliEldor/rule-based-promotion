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
        Schema::create('promotion_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('salience')->default(10);
            $table->boolean('stackable')->default(true);
            $table->boolean('is_active')->default(true); 
            // json columns for structured rule data
            $table->json('conditions')->comment('Array of condition objects');
            $table->json('actions')->comment('Array of action objects');

            $table->datetime('valid_from')->nullable();
            $table->datetime('valid_until')->nullable();
            $table->timestamps();
            
            $table->index(['salience', 'is_active']);
            $table->index(['is_active', 'valid_from', 'valid_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_rules');
    }
};
