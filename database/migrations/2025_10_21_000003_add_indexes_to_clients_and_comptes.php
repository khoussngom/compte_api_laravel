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
            // email is already unique but ensure index exists (Postgres-safe check)
            DB::statement(<<<'SQL'
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_class c WHERE c.relkind = 'i' AND c.relname = 'clients_email_index'
    ) THEN
        CREATE INDEX clients_email_index ON clients (email);
    END IF;
END$$;
SQL
            );

            DB::statement(<<<'SQL'
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_class c WHERE c.relkind = 'i' AND c.relname = 'clients_telephone_index'
    ) THEN
        CREATE INDEX clients_telephone_index ON clients (telephone);
    END IF;
END$$;
SQL
            );
        }

        if (Schema::hasTable('comptes')) {
            DB::statement(<<<'SQL'
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_class c WHERE c.relkind = 'i' AND c.relname = 'comptes_numero_compte_index'
    ) THEN
        CREATE INDEX comptes_numero_compte_index ON comptes (numero_compte);
    END IF;
END$$;
SQL
            );

            DB::statement(<<<'SQL'
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_class c WHERE c.relkind = 'i' AND c.relname = 'comptes_client_id_index'
    ) THEN
        CREATE INDEX comptes_client_id_index ON comptes (client_id);
    END IF;
END$$;
SQL
            );

            DB::statement(<<<'SQL'
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_class c WHERE c.relkind = 'i' AND c.relname = 'comptes_type_compte_index'
    ) THEN
        CREATE INDEX comptes_type_compte_index ON comptes (type_compte);
    END IF;
END$$;
SQL
            );

            DB::statement(<<<'SQL'
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_class c WHERE c.relkind = 'i' AND c.relname = 'comptes_statut_compte_index'
    ) THEN
        CREATE INDEX comptes_statut_compte_index ON comptes (statut_compte);
    END IF;
END$$;
SQL
            );
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
