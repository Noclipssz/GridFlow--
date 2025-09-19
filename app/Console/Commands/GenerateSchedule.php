<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Professor;

class GenerateSchedule extends Command
{
    protected $signature = 'schedule:generate';
    protected $description = 'Gera uma grade 5x5 (Seg-Sex, 5 aulas) usando dados do banco.';

    public function handle(): int
    {
        // Busca professores com suas matérias (já no banco)
        $professors = Professor::with('materia')
            ->whereNotNull('materia_id')
            ->get()
            ->all();

        if (empty($professors)) {
            $this->warn('Nenhum professor encontrado.');
            return self::SUCCESS;
        }

        // Mapa: materia_id => quant_aulas desejadas
        $need = [];
        foreach ($professors as $p) {
            $need[$p->materia_id] = (int)($p->materia->quant_aulas ?? 0);
        }

        $dias = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta'];
        $iterations = 0;
        $start = hrtime(true);
        $rodadas = 0;

        do {
            $iterations++;

            // Limpa grade e reseta contadores
            $grid = array_fill(0, 5, array_fill(0, 5, ''));
            $remaining = $need;

            // Ordem aleatória como no Java (Collections.shuffle)
            $order = $professors;
            shuffle($order);

            foreach ($order as $prof) {
                $matId   = $prof->materia_id;
                $matName = (string) ($prof->materia->nome ?? '');
                $target  = (int) ($remaining[$matId] ?? 0);

                if ($target <= 0 || $matName === '') {
                    continue;
                }

                // disponibilidade padrão: manhã [dia][aula]
                $h = $prof->horario_manha;
                if (!is_array($h)) {
                    $h = json_decode((string) $h, true) ?? [];
                }

                for ($i = 0; $i < 5; $i++) {
                    for ($j = 0; $j < 5; $j++) {
                        $allowed = isset($h[$i][$j]) && (int)$h[$i][$j] === 1;
                        if ($allowed && $grid[$i][$j] === '') {
                            $grid[$i][$j] = $matName;
                            $target--;
                            if ($target === 0) {
                                break 2; // próxima pessoa
                            }
                        }
                    }
                }

                $remaining[$matId] = $target;
            }

            $done = array_sum($remaining) === 0;
        } while (!$done && $iterations < 900); // trava de segurança

        // Saída
        foreach ($grid as $i => $row) {
            $this->line($dias[$i] . ':');
            foreach ($row as $j => $cell) {
                $this->line(sprintf('  Aula %d: %s', $j + 1, $cell ?: '—'));
            }
            $this->newLine();
        }

        $durationMs = (int)((hrtime(true) - $start) / 1e6);
        $this->info("Tempo de execução: {$durationMs} ms");

        $left = array_sum($remaining);


        return self::SUCCESS;
    }
}
