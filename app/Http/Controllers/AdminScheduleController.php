<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Materia;
use App\Models\Professor;

class AdminScheduleController extends Controller
{
    public function form()
    {
        // Carrega todas as matérias com seus professores
        $materias = Materia::with('professores')->orderBy('id')->get();

        return view('admin.grade', [
            'materias' => $materias,
            'grid'     => null,   // primeiro load sem grade
            'meta'     => null,
        ]);
    }

    public function generate(Request $request)
    {
        // selected[<materia_id>] = <professor_id> (ou vazio)
        $selected = (array) $request->input('selected', []);

        // Busca apenas os professores escolhidos
        $professors = Professor::with('materia')
            ->whereIn('id', array_filter(array_values($selected)))
            ->get()
            ->values();

        // Gera a grade
        [$grid, $meta] = $this->buildGrid($professors);

        // Recarrega matérias para re-renderizar o form
        $materias = Materia::with('professores')->orderBy('id')->get();

        return view('admin.grade', [
            'materias' => $materias,
            'grid'     => $grid,  // [aula][dia] para a UI
            'meta'     => $meta,  // iterações/tempo/restantes
            'selected' => $selected,
        ]);
    }

    /**
     * Monta grade 5x5 (Seg..Sex × 1ª..5ª) respeitando:
     * - horario_dp do professor (JSON [dia][aula] com 1/0)
     * - quant_aulas da matéria do professor
     *
     * UI usa [aula][dia]; horario_dp está [dia][aula] → checar invertido.
     */
    private function buildGrid($professors): array
    {
        $DAYS  = 5; // seg..sex
        $SLOTS = 5; // 1ª..5ª
        $grid = array_fill(0, $SLOTS, array_fill(0, $DAYS, ''));
        $need = [];

        foreach ($professors as $p) {
            $need[$p->id] = (int) ($p->materia->quant_aulas ?? 0);
        }

        $iterations = 0;
        $start = hrtime(true);

        do {
            $iterations++;
            // limpa a grid e contador
            $grid = array_fill(0, $SLOTS, array_fill(0, $DAYS, ''));
            $remaining = $need;

            $order = $professors->all();
            shuffle($order);

            foreach ($order as $p) {
                $toPlace = (int) ($remaining[$p->id] ?? 0);
                if ($toPlace <= 0) continue;

                $matName = $p->materia->nome ?? '—';
                // horario_dp no banco está [dia][aula]
                $h = $p->horario_dp;
                if (!is_array($h)) $h = json_decode((string)$h, true) ?? [];

                for ($d = 0; $d < $DAYS; $d++) {           // dia
                    for ($a = 0; $a < $SLOTS; $a++) {      // aula
                        $allowed = (int) ($h[$d][$a] ?? 0) === 1;
                        if ($allowed && $grid[$a][$d] === '') {
                            $grid[$a][$d] = $matName . ' — ' . $p->nome; // mostra matéria + professor
                            $toPlace--;
                            if ($toPlace === 0) break 2; // próxima pessoa
                        }
                    }
                }

                $remaining[$p->id] = $toPlace;
            }

            $done = array_sum($remaining) === 0;
        } while (!$done && $iterations < 200);

        $end = hrtime(true);

        $meta = [
            'iterations'  => $iterations,
            'duration_ms' => (int) (($end - $start) / 1e6),
            'remaining'   => $remaining ?? [],
        ];

        return [$grid, $meta];
    }
}
