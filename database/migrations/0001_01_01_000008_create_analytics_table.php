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
        Schema::create('analytics', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // barangay or sitio
            $table->string('sitio')->nullable();
            $table->integer('total_households')->default(0);
            $table->integer('total_population')->default(0);
            $table->integer('total_seniors')->default(0);
            $table->integer('total_pwd')->default(0);
            $table->integer('total_children')->default(0);
            $table->integer('total_adults')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics');
    }
};