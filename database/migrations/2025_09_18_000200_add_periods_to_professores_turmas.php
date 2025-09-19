<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('professores', function (Blueprint $table) {
            $table->json('horario_manha')->nullable()->after('horario_dp');
            $table->json('horario_tarde')->nullable()->after('horario_manha');
            $table->json('horario_noite')->nullable()->after('horario_tarde');
        });

        Schema::table('turmas', function (Blueprint $table) {
            $table->json('horario_manha')->nullable()->after('horario_dp');
            $table->json('horario_tarde')->nullable()->after('horario_manha');
            $table->json('horario_noite')->nullable()->after('horario_tarde');
        });
    }

    public function down(): void
    {
        Schema::table('professores', function (Blueprint $table) {
            $table->dropColumn(['horario_manha', 'horario_tarde', 'horario_noite']);
        });

        Schema::table('turmas', function (Blueprint $table) {
            $table->dropColumn(['horario_manha', 'horario_tarde', 'horario_noite']);
        });
    }
};

