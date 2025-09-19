<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Materia;
use App\Models\Professor;
use App\Models\Turma;

class AdminScheduleController extends Controller
{
    public function form()
    {
        // Carrega todas as matérias com seus professores
        $materias = Materia::with('professores')->orderBy('id')->get();
        $periodo = request()->input('periodo', 'manha');
        $allTurmas = Turma::orderBy('id')->get();
        // Somente turmas do período selecionado
        $turmas = $allTurmas->filter(fn($t) => $t->periodo === $periodo && !$this->turmaHasSchedule($t, $periodo));
        $turmasLocked = $allTurmas->filter(fn($t) => $t->periodo === $periodo && $this->turmaHasSchedule($t, $periodo));

        return view('admin.grade', [
            'materias' => $materias,
            'turmas'        => $turmas,
            'turmasLocked'  => $turmasLocked,
            'grid'     => null,   // primeiro load sem grade
            'meta'     => null,
            'periodo'  => $periodo,
        ]);
    }

    public function generate(Request $request)
    {
        // turma obrigatória para gerar/guardar grade
        $data = $request->validate([
            'turma_id' => ['required', 'integer', 'exists:turmas,id'],
            'selected' => ['array'], // selected[<materia_id>] = <professor_id>
            'periodo'  => ['required', 'in:manha,tarde,noite'],
        ]);
        $turma = Turma::findOrFail((int) $data['turma_id']);
        if ($turma->periodo !== $data['periodo'] || $this->turmaHasSchedule($turma, $data['periodo'])) {
            return redirect()->route('admin.grade.form')
                ->withErrors(['turma_id' => 'Turma inválida para o período selecionado ou já possui grade.'])
                ->withInput();
        }

        // selected[<materia_id>] = <professor_id> (ou vazio)
        $selected = (array) ($data['selected'] ?? []);

        // Busca apenas os professores escolhidos
        $professors = Professor::with('materia')
            ->whereIn('id', array_filter(array_values($selected)))
            ->get()
            ->values();

        // Gera a grade (labels p/ UI e IDs p/ salvar) — prévia, sem persistir
        [$grid, $meta, $idsGrid] = $this->buildGrid($professors, $data['periodo']);

        // Recarrega matérias/turmas para re-renderizar o form (respeitando o período)
        $materias   = Materia::with('professores')->orderBy('id')->get();
        $allTurmas  = Turma::orderBy('id')->get();
        $turmas     = $allTurmas->filter(fn($t) => $t->periodo === $data['periodo'] && !$this->turmaHasSchedule($t, $data['periodo']));
        $turmasLocked = $allTurmas->filter(fn($t) => $t->periodo === $data['periodo'] && $this->turmaHasSchedule($t, $data['periodo']));

        return view('admin.grade', [
            'materias' => $materias,
            'turmas'        => $turmas,
            'turmasLocked'  => $turmasLocked,
            'grid'     => $grid,  // [aula][dia] para a UI (labels)
            'meta'     => $meta,  // iterações/tempo/restantes
            'selected' => $selected,
            'selected_turma_id' => $turma->id,
            // carrega a matriz de IDs para o botão "Salvar grade"
            'grid_ids' => $idsGrid,
            'periodo'  => $data['periodo'],
        ]);
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'turma_id' => ['required', 'integer', 'exists:turmas,id'],
            'grid_ids' => ['required', 'json'],
            'periodo'  => ['required', 'in:manha,tarde,noite'],
        ]);

        $turma = Turma::findOrFail((int) $data['turma_id']);
        if ($turma->periodo !== $data['periodo'] || $this->turmaHasSchedule($turma, $data['periodo'])) {
            return back()->withErrors(['turma_id' => 'Turma inválida para o período selecionado ou já possui grade.'])->withInput();
        }
        $idsGrid = json_decode((string) $data['grid_ids'], true) ?? [];

        DB::transaction(function () use ($turma, $idsGrid, $data) {
            $DAYS = 5; $SLOTS = 5;

            // 1) Reverter alocações anteriores desta turma (se houver), sem afetar outras turmas
            $prev = $this->getTurmaGrid($turma, $data['periodo']);
            if (!is_array($prev)) { $prev = json_decode((string) $prev, true) ?? []; }

            $otherTurmas = Turma::where('id', '!=', $turma->id)->get();
            $stillAllocated = [];
            foreach ($otherTurmas as $ot) {
                $g = $this->getTurmaGrid($ot, $data['periodo']);
                if (!is_array($g)) $g = json_decode((string) $g, true) ?? [];
                for ($a = 0; $a < $SLOTS; $a++) {
                    $row = $g[$a] ?? [];
                    for ($d = 0; $d < $DAYS; $d++) {
                        $cell = $row[$d] ?? null;
                        if (is_array($cell) && !empty($cell['professor_id'])) {
                            $pid = (int) $cell['professor_id'];
                            $stillAllocated[$pid][$d][$a] = true;
                        }
                    }
                }
            }

            $toRestoreByProf = [];
            for ($a = 0; $a < $SLOTS; $a++) {
                $row = $prev[$a] ?? [];
                for ($d = 0; $d < $DAYS; $d++) {
                    $slot = $row[$d] ?? null; // [aula][dia]
                    if (is_array($slot) && !empty($slot['professor_id'])) {
                        $pid = (int) $slot['professor_id'];
                        $toRestoreByProf[$pid][] = [$d, $a]; // (dia, aula)
                    }
                }
            }
            if ($toRestoreByProf) {
                $professores = Professor::whereIn('id', array_keys($toRestoreByProf))->get()->keyBy('id');
                foreach ($toRestoreByProf as $pid => $coords) {
                    if (!isset($professores[$pid])) continue;
                    $h = $this->getProfGrid($professores[$pid], $data['periodo']);
                    if (!is_array($h)) $h = json_decode((string) $h, true) ?? [];
                    // normaliza grade 5x5
                    for ($d = 0; $d < $DAYS; $d++) {
                        if (!isset($h[$d]) || !is_array($h[$d])) $h[$d] = array_fill(0, $SLOTS, 0);
                        $h[$d] = array_pad(array_slice($h[$d], 0, $SLOTS), $SLOTS, 0);
                    }
                    foreach ($coords as [$d, $a]) {
                        if (($h[$d][$a] ?? 0) === 2) {
                            $hasOther = !empty($stillAllocated[$pid][$d][$a]);
                            if (!$hasOther) {
                                $h[$d][$a] = 1; // volta a disponível
                            }
                        }
                    }
                    $this->setProfGrid($professores[$pid], $data['periodo'], $h);
                    $professores[$pid]->save();
                }
            }

            // 2) Salvar nova grade da turma (IDs estruturados)
            $this->setTurmaGrid($turma, $data['periodo'], $idsGrid);
            $turma->save();

            // 3) Marcar nos professores: slots utilizados = 2 (aula)
            $byProf = [];
            for ($a = 0; $a < $SLOTS; $a++) {
                $row = $idsGrid[$a] ?? [];
                for ($d = 0; $d < $DAYS; $d++) {
                    $cell = $row[$d] ?? null;
                    if (is_array($cell) && !empty($cell['professor_id'])) {
                        $pid = (int) $cell['professor_id'];
                        $byProf[$pid][] = [$d, $a];
                    }
                }
            }
            if ($byProf) {
                $professores = Professor::whereIn('id', array_keys($byProf))->get()->keyBy('id');
                foreach ($byProf as $pid => $coords) {
                    if (!isset($professores[$pid])) continue;
                    $h = $this->getProfGrid($professores[$pid], $data['periodo']);
                    if (!is_array($h)) $h = json_decode((string) $h, true) ?? [];
                    // normaliza grade 5x5
                    for ($d = 0; $d < $DAYS; $d++) {
                        if (!isset($h[$d]) || !is_array($h[$d])) $h[$d] = array_fill(0, $SLOTS, 0);
                        $h[$d] = array_pad(array_slice($h[$d], 0, $SLOTS), $SLOTS, 0);
                    }
                    foreach ($coords as [$d, $a]) {
                        $h[$d][$a] = 2; // aula
                    }
                    $this->setProfGrid($professores[$pid], $data['periodo'], $h);
                    $professores[$pid]->save();
                }
            }
        });

        return redirect()->route('admin.grade.form', ['periodo' => $data['periodo']])->with('ok', 'Grade salva com sucesso.');
    }

    /**
     * Monta grade 5x5 (Seg..Sex × 1ª..5ª) respeitando:
     * - horário do professor por período (JSON [dia][aula] com 1/0)
     * - quant_aulas da matéria do professor
     *
     * UI usa [aula][dia]; no banco está [dia][aula] → checar invertido.
     */
    private function buildGrid($professors, string $periodo): array
    {
        $DAYS  = 5; // seg..sex
        $SLOTS = 5; // 1ª..5ª
        $gridLabels = array_fill(0, $SLOTS, array_fill(0, $DAYS, ''));
        $gridIds    = array_fill(0, $SLOTS, array_fill(0, $DAYS, null));
        $need = [];

        foreach ($professors as $p) {
            $need[$p->id] = (int) ($p->materia->quant_aulas ?? 0);
        }

        $iterations = 0;
        $start = hrtime(true);

        do {
            $iterations++;
            // limpa a grid e contador
            $gridLabels = array_fill(0, $SLOTS, array_fill(0, $DAYS, ''));
            $gridIds    = array_fill(0, $SLOTS, array_fill(0, $DAYS, null));
            $remaining = $need;

            $order = $professors->all();
            shuffle($order);

            foreach ($order as $p) {
                $toPlace = (int) ($remaining[$p->id] ?? 0);
                if ($toPlace <= 0) continue;

                $matName = $p->materia->nome ?? '—';
                // disponibilidade no banco está [dia][aula] no período escolhido
                $h = $this->getProfGrid($p, $periodo);
                if (!is_array($h)) $h = json_decode((string)$h, true) ?? [];

                for ($d = 0; $d < $DAYS; $d++) {           // dia
                    for ($a = 0; $a < $SLOTS; $a++) {      // aula
                        $cell = (int) ($h[$d][$a] ?? 0);
                        $allowed = $cell === 1; // 1 = disponível; 2 = já tem aula
                        if ($allowed && $gridLabels[$a][$d] === '') {
                            $gridLabels[$a][$d] = $matName . ' — ' . $p->nome; // mostra matéria + professor
                            $gridIds[$a][$d] = [
                                'professor_id' => $p->id,
                                'materia_id'   => $p->materia->id ?? null,
                            ];
                            $toPlace--;
                            if ($toPlace === 0) break 2; // próxima pessoa
                        }
                    }
                }

                $remaining[$p->id] = $toPlace;
            }

            $done = array_sum($remaining) === 0;
        } while (!$done && $iterations < 10000);

        $end = hrtime(true);

        $meta = [
            'iterations'  => $iterations,
            'duration_ms' => (int) (($end - $start) / 1e6),
            'remaining'   => $remaining ?? [],
        ];

        return [$gridLabels, $meta, $gridIds];
    }

    private function turmaHasSchedule(Turma $turma, string $periodo = 'manha'): bool
    {
        $grid = $this->getTurmaGrid($turma, $periodo);
        if (!is_array($grid)) $grid = json_decode((string) $grid, true) ?? [];
        for ($a = 0; $a < 5; $a++) {
            $row = $grid[$a] ?? [];
            for ($d = 0; $d < 5; $d++) {
                $cell = $row[$d] ?? null;
                if (is_array($cell) && (!empty($cell['professor_id']) || !empty($cell['materia_id']))) {
                    return true;
                }
            }
        }
        return false;
    }

    public function clear(Request $request, Turma $turma)
    {
        $periodo = $request->input('periodo', 'manha');
        // Libera a turma: remove alocações desta turma e restaura 2->1 nos professores, sem afetar outras turmas
        if ($turma->periodo !== $periodo) {
            return redirect()->route('admin.grade.form', ['periodo' => $periodo])
                ->withErrors(['turma_id' => 'Turma não pertence ao período selecionado.']);
        }

        DB::transaction(function () use ($turma, $periodo) {
            $DAYS = 5; $SLOTS = 5;

            // Grade atual desta turma
            $prev = $this->getTurmaGrid($turma, $periodo);
            if (!is_array($prev)) { $prev = json_decode((string) $prev, true) ?? []; }

            // Marca slots ainda alocados em outras turmas
            $otherTurmas = Turma::where('id', '!=', $turma->id)->get();
            $stillAllocated = [];
            foreach ($otherTurmas as $ot) {
                $g = $this->getTurmaGrid($ot, $periodo);
                if (!is_array($g)) $g = json_decode((string) $g, true) ?? [];
                for ($a = 0; $a < $SLOTS; $a++) {
                    $row = $g[$a] ?? [];
                    for ($d = 0; $d < $DAYS; $d++) {
                        $cell = $row[$d] ?? null;
                        if (is_array($cell) && !empty($cell['professor_id'])) {
                            $pid = (int) $cell['professor_id'];
                            $stillAllocated[$pid][$d][$a] = true;
                        }
                    }
                }
            }

            // Coleta por professor os slots desta turma a restaurar
            $toRestoreByProf = [];
            for ($a = 0; $a < $SLOTS; $a++) {
                $row = $prev[$a] ?? [];
                for ($d = 0; $d < $DAYS; $d++) {
                    $slot = $row[$d] ?? null; // [aula][dia]
                    if (is_array($slot) && !empty($slot['professor_id'])) {
                        $pid = (int) $slot['professor_id'];
                        $toRestoreByProf[$pid][] = [$d, $a]; // (dia, aula)
                    }
                }
            }
            if ($toRestoreByProf) {
                $professores = Professor::whereIn('id', array_keys($toRestoreByProf))->get()->keyBy('id');
                foreach ($toRestoreByProf as $pid => $coords) {
                    if (!isset($professores[$pid])) continue;
                    $h = $this->getProfGrid($professores[$pid], $periodo);
                    if (!is_array($h)) $h = json_decode((string) $h, true) ?? [];
                    // normaliza grade 5x5
                    for ($d = 0; $d < $DAYS; $d++) {
                        if (!isset($h[$d]) || !is_array($h[$d])) $h[$d] = array_fill(0, $SLOTS, 0);
                        $h[$d] = array_pad(array_slice($h[$d], 0, $SLOTS), $SLOTS, 0);
                    }
                    foreach ($coords as [$d, $a]) {
                        if (($h[$d][$a] ?? 0) === 2) {
                            $hasOther = !empty($stillAllocated[$pid][$d][$a]);
                            if (!$hasOther) {
                                $h[$d][$a] = 1; // volta a disponível
                            }
                        }
                    }
                    $this->setProfGrid($professores[$pid], $periodo, $h);
                    $professores[$pid]->save();
                }
            }

            // Limpa grade da turma (deixa null)
            $this->setTurmaGrid($turma, $periodo, null);
            $turma->save();
        });

        return redirect()->route('admin.grade.form', ['periodo' => $periodo])->with('ok', 'Turma liberada para nova geração.');
    }

    private function getProfGrid(Professor $p, string $periodo)
    {
        return match($periodo) {
            'manha' => $p->horario_manha,
            'tarde' => $p->horario_tarde,
            'noite' => $p->horario_noite,
        };
    }

    private function setProfGrid(Professor $p, string $periodo, $grid): void
    {
        match($periodo) {
            'manha' => $p->horario_manha = $grid,
            'tarde' => $p->horario_tarde = $grid,
            'noite' => $p->horario_noite = $grid,
        };
    }

    private function getTurmaGrid(Turma $t, string $periodo)
    {
        return match($periodo) {
            'manha' => $t->horario_manha,
            'tarde' => $t->horario_tarde,
            'noite' => $t->horario_noite,
        };
    }

    private function setTurmaGrid(Turma $t, string $periodo, $grid): void
    {
        match($periodo) {
            'manha' => $t->horario_manha = $grid,
            'tarde' => $t->horario_tarde = $grid,
            'noite' => $t->horario_noite = $grid,
        };
    }
}
