<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comptes', function (Blueprint $table) {
            if (!Schema::hasColumn('comptes', 'devise')) {
                $table->string('devise', 10)->default('FCFA')->after('solde');
            }
            if (!Schema::hasColumn('comptes', 'motif_blocage')) {
                $table->text('motif_blocage')->nullable()->after('statut_compte');
            }
            if (!Schema::hasColumn('comptes', 'version')) {
                $table->integer('version')->default(1)->after('motif_blocage');
            }
        });
    }

    public function down(): void
    {
        Schema::table('comptes', function (Blueprint $table) {
            if (Schema::hasColumn('comptes', 'version')) {
                $table->dropColumn('version');
            }
            if (Schema::hasColumn('comptes', 'motif_blocage')) {
                $table->dropColumn('motif_blocage');
            }
            if (Schema::hasColumn('comptes', 'devise')) {
                $table->dropColumn('devise');
            }
        });
    }
};
