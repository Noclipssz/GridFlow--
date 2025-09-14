<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('professores', function (Blueprint $table) {
            // CPF: 11 dígitos sem pontuação. Unique e indexado.
            $table->char('cpf', 11)->nullable()->after('nome')->unique();

            // Senha (hash). Deixe nullable por enquanto para não quebrar registros existentes;
            // depois que popular, pode tornar notNullable em outra migração, se quiser.
            $table->string('senha')->nullable()->after('cpf');
        });
    }

    public function down(): void
    {
        Schema::table('professores', function (Blueprint $table) {
            $table->dropUnique(['cpf']);
            $table->dropColumn(['cpf', 'senha']);
        });
    }
};
