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
        Schema::create("materia_turma", function (Blueprint $table) {
            $table->foreignId("materia_id")->constrained("materias")->onDelete("cascade");
            $table->foreignId("turma_id")->constrained("turmas")->onDelete("cascade");
            $table->primary(["materia_id", "turma_id"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("materia_turma");
    }
};


