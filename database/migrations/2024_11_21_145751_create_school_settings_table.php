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
        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->string('school_name');
            $table->string('current_year');
            $table->string('term_start_date');
            $table->string('term_end_date');
            $table->string('term')->nullable();
            $table->string('school_motto')->nullable();
            $table->string('school_vision')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('next_term_start_date')->nullable();
            $table->string('next_term_end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_settings');
    }
};
