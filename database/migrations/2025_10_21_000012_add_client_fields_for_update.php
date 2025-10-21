<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (!Schema::hasColumn('clients', 'titulaire')) {
                $table->string('titulaire')->nullable()->after('prenom');
            }
            if (!Schema::hasColumn('clients', 'statut')) {
                $table->string('statut')->default('Actif')->after('adresse');
            }
            if (!Schema::hasColumn('clients', 'version')) {
                $table->integer('version')->default(1)->after('statut');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            if (Schema::hasColumn('clients', 'version')) {
                $table->dropColumn('version');
            }
            if (Schema::hasColumn('clients', 'statut')) {
                $table->dropColumn('statut');
            }
            if (Schema::hasColumn('clients', 'titulaire')) {
                $table->dropColumn('titulaire');
            }
        });
    }
};
