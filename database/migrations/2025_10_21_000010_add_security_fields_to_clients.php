<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'nci')) {
                $table->string('nci')->nullable()->unique()->after('email');
            }
            if (!Schema::hasColumn('clients', 'security_code')) {
                $table->string('security_code')->nullable()->after('nci');
            }
            if (!Schema::hasColumn('clients', 'require_code_on_login')) {
                $table->boolean('require_code_on_login')->default(true)->after('security_code');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (Schema::hasColumn('clients', 'require_code_on_login')) {
                $table->dropColumn('require_code_on_login');
            }
            if (Schema::hasColumn('clients', 'security_code')) {
                $table->dropColumn('security_code');
            }
            if (Schema::hasColumn('clients', 'nci')) {
                $table->dropUnique(['nci']);
                $table->dropColumn('nci');
            }
        });
    }
};
