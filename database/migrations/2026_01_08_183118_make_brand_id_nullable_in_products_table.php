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
        Schema::table('products', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['brand_id']);
            
            // Make the column nullable
            $table->unsignedBigInteger('brand_id')->nullable()->change();
            
            // Re-add the foreign key constraint with nullable support
            $table->foreign('brand_id')->references('id')->on('brands')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['brand_id']);
            
            // Make the column non-nullable again
            $table->unsignedBigInteger('brand_id')->nullable(false)->change();
            
            // Re-add the foreign key constraint
            $table->foreign('brand_id')->references('id')->on('brands')->onUpdate('cascade');
        });
    }
};
