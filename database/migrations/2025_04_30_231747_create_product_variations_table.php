<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->json('option_ids'); // Example: [3, 7, 5] => Red, Large
            $table->timestamps();
        });
        Schema::table('product_variations', function (Blueprint $table) {
            DB::statement("ALTER TABLE product_variations ADD COLUMN attribute_option_index VARCHAR(191) GENERATED ALWAYS AS (JSON_UNQUOTE(JSON_EXTRACT(option_ids, '$[0]'))) STORED");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variations');
    }
};
