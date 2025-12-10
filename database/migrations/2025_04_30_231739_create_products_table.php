<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\User;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->longText('description')->nullable();
            $table->decimal('base_price', 10, 2)->default(0); // Used when no variations
            $table->integer('stock')->default(0); // Used when no variations

            $table->boolean('has_variations')->default(false);

            $table->foreignIdFor(Category::class)->constrained()->cascadeOnUpdate();
            $table->foreignIdFor(Brand::class)->constrained()->cascadeOnUpdate();


            $table->boolean('has_discount')->default(false);
            $table->enum('discount_type', ['fixed', 'percentage'])->nullable();
            $table->decimal('discount_value', 10, 2)->nullable();

            $table->foreignIdFor(User::class, 'created_by')->constrained('users')->cascadeOnUpdate();

            $table->boolean('featured')->default(false);
            $table->boolean('new')->default(false);
            $table->boolean('top')->default(false);

            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
