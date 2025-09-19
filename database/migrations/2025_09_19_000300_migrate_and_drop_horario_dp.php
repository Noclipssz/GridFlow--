<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Copia dados de horario_dp para períodos quando estes estiverem nulos
        // Professores
        if (Schema::hasTable('professores')) {
            $rows = DB::table('professores')->select('id', 'horario_dp', 'horario_manha', 'horario_tarde', 'horario_noite')->get();
            foreach ($rows as $r) {
                $dp = $r->horario_dp;
                if ($dp !== null) {
                    $manha = $r->horario_manha;
                    $tarde = $r->horario_tarde;
                    $noite = $r->horario_noite;
                    $upd = [];
                    if ($manha === null) $upd['horario_manha'] = $dp;
                    if ($tarde === null) $upd['horario_tarde'] = $dp;
                    if ($noite === null) $upd['horario_noite'] = $dp;
                    if ($upd) {
                        DB::table('professores')->where('id', $r->id)->update($upd);
                    }
                }
            }
        }

        // Turmas
        if (Schema::hasTable('turmas')) {
            $rows = DB::table('turmas')->select('id', 'horario_dp', 'horario_manha', 'horario_tarde', 'horario_noite')->get();
            foreach ($rows as $r) {
                $dp = $r->horario_dp;
                if ($dp !== null) {
                    $manha = $r->horario_manha;
                    $tarde = $r->horario_tarde;
                    $noite = $r->horario_noite;
                    $upd = [];
                    if ($manha === null) $upd['horario_manha'] = $dp;
                    if ($tarde === null) $upd['horario_tarde'] = $dp;
                    if ($noite === null) $upd['horario_noite'] = $dp;
                    if ($upd) {
                        DB::table('turmas')->where('id', $r->id)->update($upd);
                    }
                }
            }
        }

        // Remoção das colunas legado
        Schema::table('professores', function (Blueprint $table) {
            if (Schema::hasColumn('professores', 'horario_dp')) {
                $table->dropColumn('horario_dp');
            }
        });
        Schema::table('turmas', function (Blueprint $table) {
            if (Schema::hasColumn('turmas', 'horario_dp')) {
                $table->dropColumn('horario_dp');
            }
        });
    }

    public function down(): void
    {
        // Restaura as colunas (vazias) para compatibilidade reversa
        Schema::table('professores', function (Blueprint $table) {
            if (!Schema::hasColumn('professores', 'horario_dp')) {
                $table->json('horario_dp')->nullable()->after('materia_id');
            }
        });
        Schema::table('turmas', function (Blueprint $table) {
            if (!Schema::hasColumn('turmas', 'horario_dp')) {
                $table->json('horario_dp')->nullable()->after('nome');
            }
        });
    }
};

