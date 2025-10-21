<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comptes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('numero_compte')->unique()->index();
            $table->string('titulaire_compte');
            $table->string('type_compte');
            $table->double('solde')->default(0);
            $table->date('date_creation')->nullable();
            $table->string('statut_compte')->default('Actif');
            $table->uuid('client_id');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comptes');
    }
};
