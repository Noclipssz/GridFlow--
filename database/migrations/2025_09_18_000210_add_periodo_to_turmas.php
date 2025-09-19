<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('turmas', function (Blueprint $table) {
            $table->enum('periodo', ['manha','tarde','noite'])->default('manha')->after('nome');
            $table->index('periodo');
        });
    }

    public function down(): void
    {
        Schema::table('turmas', function (Blueprint $table) {
            $table->dropIndex(['periodo']);
            $table->dropColumn('periodo');
        });
    }
};

