<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tracking_number', 32)->unique();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->nullable();
            $table->string('shipping_option_id', 64)->nullable();
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->string('payment_method', 32)->default('cash_on_delivery'); // cash_on_delivery, online
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax_estimate', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->json('shipping_address')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 32)->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
