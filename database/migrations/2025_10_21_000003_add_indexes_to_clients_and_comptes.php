<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Use raw SQL with IF NOT EXISTS for idempotence (Postgres/MariaDB/MySQL compatibility varies)
        if (Schema::hasTable('clients')) {
            // email is already unique but ensure index exists
            DB::statement("CREATE INDEX IF NOT EXISTS clients_email_index ON clients (email);");
            DB::statement("CREATE INDEX IF NOT EXISTS clients_telephone_index ON clients (telephone);");
        }

        if (Schema::hasTable('comptes')) {
            DB::statement("CREATE INDEX IF NOT EXISTS comptes_numero_compte_index ON comptes (numero_compte);");
            DB::statement("CREATE INDEX IF NOT EXISTS comptes_client_id_index ON comptes (client_id);");
            DB::statement("CREATE INDEX IF NOT EXISTS comptes_type_compte_index ON comptes (type_compte);");
            DB::statement("CREATE INDEX IF NOT EXISTS comptes_statut_compte_index ON comptes (statut_compte);");
        }
    }

    public function down(): void
    {
        // Drop indexes if they exist
        DB::statement("DROP INDEX IF EXISTS clients_email_index;");
        DB::statement("DROP INDEX IF EXISTS clients_telephone_index;");
        DB::statement("DROP INDEX IF EXISTS comptes_numero_compte_index;");
        DB::statement("DROP INDEX IF EXISTS comptes_client_id_index;");
        DB::statement("DROP INDEX IF EXISTS comptes_type_compte_index;");
        DB::statement("DROP INDEX IF EXISTS comptes_statut_compte_index;");
    }
};
