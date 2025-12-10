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
        Schema::create('cms_page_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cms_page_id')->constrained()->onDelete('cascade');
            $table->string('section_name'); 
            $table->enum('section_type', ['single', 'repeater'])->default('single');
            $table->integer('section_sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_page_sections');
    }
};
