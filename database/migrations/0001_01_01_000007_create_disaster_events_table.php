<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Disaster events feature has been removed from SafeTrack.
     * This migration is kept for backwards compatibility but does nothing.
     */
    public function up(): void
    {
        // Disaster events feature removed
    }

    public function down(): void
    {
        // No-op
    }
};