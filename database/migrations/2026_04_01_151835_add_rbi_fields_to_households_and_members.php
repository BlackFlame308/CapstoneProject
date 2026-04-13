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
        Schema::table('households', function (Blueprint $table) {
            $table->string('region')->nullable()->after('contact_number');
            $table->string('province')->nullable()->after('region');
            $table->string('city_mun')->nullable()->after('province');
            $table->string('barangay')->nullable()->after('city_mun');
            $table->string('household_number')->nullable()->after('barangay');
        });

        Schema::table('members', function (Blueprint $table) {
            $table->string('philips_card_no')->nullable()->after('last_name');
            $table->string('residence_address')->nullable()->after('religion');
            $table->string('date_accomplished')->nullable()->after('profession');
            $table->string('name_signature')->nullable()->after('date_accomplished');
            $table->string('attested_by')->nullable()->after('name_signature');
            $table->boolean('left_thumbmark')->default(false)->after('attested_by');
            $table->boolean('right_thumbmark')->default(false)->after('left_thumbmark');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('households', function (Blueprint $table) {
            $table->dropColumn(['region', 'province', 'city_mun', 'barangay', 'household_number']);
        });

        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['philips_card_no', 'residence_address', 'education_level', 'date_accomplished', 'name_signature', 'attested_by', 'left_thumbmark', 'right_thumbmark']);
        });
    }
};
