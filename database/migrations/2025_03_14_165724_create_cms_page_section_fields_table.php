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
        Schema::create('cms_page_section_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cms_page_section_id')->constrained()->onDelete('cascade');
            $table->string('field_group')->nullable(); // For repeatable fields
            $table->string('field_name');
            $table->enum('field_type', ['text', 'textarea', 'image', 'number', 'boolean', 'select']);
            $table->text('field_value')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_page_section_fields');
    }
};
