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
        Schema::create('rule_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id')->constrained('promotion_rules')->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->string('order_reference', 100)->nullable();
            $table->json('line_item_data')->comment('Line item data when rule was applied');
            $table->json('customer_data')->comment('Customer data when rule was applied');
            $table->decimal('discount_amount', 10, 2);
            $table->timestamps();

            $table->index(['rule_id', 'created_at']);
            $table->index(['customer_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rule_applications');
    }
};
