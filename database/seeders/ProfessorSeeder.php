<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Professor;
use App\Models\Materia;

class ProfessorSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            ['nome' => 'Ana Silva',   'cpf' => '11122233344', 'materia' => 'Matemática'],
            ['nome' => 'Bruno Lima',  'cpf' => '22233344455', 'materia' => 'Português'],
            ['nome' => 'Carla Souza', 'cpf' => '33344455566', 'materia' => 'História'],
        ];

        foreach ($map as $row) {
            $materia = Materia::where('nome', $row['materia'])->first();
            if (!$materia) {
                $materia = Materia::create(['nome' => $row['materia'], 'quant_aulas' => 2, 'check' => true]);
            }

            Professor::updateOrCreate(
                ['cpf' => $row['cpf']],
                [
                    'nome' => $row['nome'],
                    'materia_id' => $materia->id,
                    'senha' => 'senha123',
                    // inicia sem grade definida
                    'horario_manha' => null,
                    'horario_tarde' => null,
                    'horario_noite' => null,
                ]
            );
        }
    }
}

