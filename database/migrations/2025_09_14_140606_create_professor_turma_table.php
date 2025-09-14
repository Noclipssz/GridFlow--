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
        Schema::create("professor_turma", function (Blueprint $table) {
            $table->foreignId("professor_id")->constrained("professores")->onDelete("cascade");
            $table->foreignId("turma_id")->constrained("turmas")->onDelete("cascade");
            $table->primary(["professor_id", "turma_id"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("professor_turma");
    }
};


