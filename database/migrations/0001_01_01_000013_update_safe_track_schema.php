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
        // Ensure role_id reference on users for RBAC
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'role_id')) {
                    $table->unsignedBigInteger('role_id')->nullable()->after('password');
                    $table->foreign('role_id')->references('id')->on('roles')->nullOnDelete();
                }
            });
        }

        // Ensure households has sitio field
        if (Schema::hasTable('households')) {
            Schema::table('households', function (Blueprint $table) {
                if (!Schema::hasColumn('households', 'sitio')) {
                    $table->string('sitio')->nullable()->after('address');
                }
            });
        }

        // Extend members with required fields
        if (Schema::hasTable('members')) {
            Schema::table('members', function (Blueprint $table) {
                $cols = Schema::getColumnListing('members');

                if (!in_array('first_name', $cols, true)) {
                    $table->string('first_name')->nullable()->after('household_id');
                }
                if (!in_array('middle_name', $cols, true)) {
                    $table->string('middle_name')->nullable()->after('first_name');
                }
                if (!in_array('last_name', $cols, true)) {
                    $table->string('last_name')->nullable()->after('middle_name');
                }
                if (!in_array('suffix', $cols, true)) {
                    $table->string('suffix')->nullable()->after('last_name');
                }
                if (!in_array('birth_date', $cols, true)) {
                    $table->date('birth_date')->nullable()->after('suffix');
                }
                if (!in_array('birth_place', $cols, true)) {
                    $table->string('birth_place')->nullable()->after('birth_date');
                }
                if (!in_array('sex', $cols, true)) {
                    $table->enum('sex', ['male', 'female', 'other'])->nullable()->after('birth_place');
                }
                if (!in_array('civil_status', $cols, true)) {
                    $table->string('civil_status')->nullable()->after('sex');
                }
                if (!in_array('religion', $cols, true)) {
                    $table->string('religion')->nullable()->after('civil_status');
                }
                if (!in_array('citizenship', $cols, true)) {
                    $table->string('citizenship')->nullable()->after('religion');
                }
                if (!in_array('profession', $cols, true)) {
                    $table->string('profession')->nullable()->after('citizenship');
                }
                if (!in_array('contact_number', $cols, true)) {
                    $table->string('contact_number')->nullable()->after('profession');
                }
                if (!in_array('email', $cols, true)) {
                    $table->string('email')->nullable()->after('contact_number');
                }
                if (!in_array('education_level', $cols, true)) {
                    $table->string('education_level')->nullable()->after('email');
                }
                if (!in_array('is_graduate', $cols, true)) {
                    $table->boolean('is_graduate')->default(false)->after('education_level');
                }
                if (!in_array('is_pwd', $cols, true)) {
                    $table->boolean('is_pwd')->default(false)->after('is_graduate');
                }
            });
        }

        // Align disaster events naming
        // Removed - disaster events feature removed from system

        // Create reports table
        if (!Schema::hasTable('reports')) {
            Schema::create('reports', function (Blueprint $table) {
                $table->id();
                $table->string('type');
                $table->json('content');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('reports')) {
            Schema::dropIfExists('reports');
        }

        if (Schema::hasTable('members')) {
            Schema::table('members', function (Blueprint $table) {
                $cols = ['first_name', 'middle_name', 'last_name', 'suffix', 'birth_date', 'birth_place', 'sex', 'civil_status', 'religion', 'citizenship', 'profession', 'contact_number', 'email', 'education_level', 'is_graduate', 'is_pwd'];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('members', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('households')) {
            Schema::table('households', function (Blueprint $table) {
                if (Schema::hasColumn('households', 'sitio')) {
                    $table->dropColumn('sitio');
                }
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (Schema::hasColumn('users', 'role_id')) {
                    if (Schema::hasColumn('users', 'role_id')) {
                        $table->dropForeign(['role_id']);
                        $table->dropColumn('role_id');
                    }
                }
            });
        }
    }
};
