<?php

namespace App\Services;

use App\Models\Professor;
use App\Models\Turma;
use Illuminate\Support\Facades\DB;

class GradeService
{
    /**
     * Monta grade 5x5 (Seg..Sex × 1ª..5ª) respeitando disponibilidade do professor
     * no período e a quantidade de aulas da matéria do professor.
     * Retorna [gridLabels [aula][dia] string, meta, gridIds [aula][dia] array].
     *
     * @param \Illuminate\Support\Collection<int, Professor>|array<int, Professor> $professors
     */
    public function buildGrid($professors, string $periodo): array
    {
        // Estratégia 1: algoritmo original
        [$grid1, $meta1, $ids1] = $this->buildGridOriginal($professors, $periodo);
        if (array_sum((array) ($meta1['remaining'] ?? [])) === 0) {
            $meta1['strategy'] = 'original';
            return [$grid1, $meta1, $ids1];
        }

        // Estratégia 2: heurística melhorada
        [$grid2, $meta2, $ids2] = $this->buildGridHeuristic($professors, $periodo, 32);
        if (array_sum((array) ($meta2['remaining'] ?? [])) === 0) {
            $meta2['strategy'] = 'heuristic';
            return [$grid2, $meta2, $ids2];
        }

        // Estratégia 3: orçamento de tempo
        [$grid3, $meta3, $ids3] = $this->buildGridTimeBudget($professors, $periodo, 150);
        $meta3['strategy'] = 'time_budget';
        return [$grid3, $meta3, $ids3];
    }

    private function buildGridOriginal($professors, string $periodo): array
    {
        $DAYS  = 5; $SLOTS = 5;
        $gridLabels = array_fill(0, $SLOTS, array_fill(0, $DAYS, ''));
        $gridIds    = array_fill(0, $SLOTS, array_fill(0, $DAYS, null));
        $need = [];

        $profs = is_array($professors) ? $professors : $professors->all();
        foreach ($profs as $p) {
            $need[$p->id] = (int) ($p->materia->quant_aulas ?? 0);
        }

        $iterations = 0;
        $start = hrtime(true);

        do {
            $iterations++;
            $gridLabels = array_fill(0, $SLOTS, array_fill(0, $DAYS, ''));
            $gridIds    = array_fill(0, $SLOTS, array_fill(0, $DAYS, null));
            $remaining = $need;

            $order = $profs;
            shuffle($order);

            foreach ($order as $p) {
                $toPlace = (int) ($remaining[$p->id] ?? 0);
                if ($toPlace <= 0) continue;

                $matName = $p->materia->nome ?? '—';
                $h = $this->getProfGrid($p, $periodo);
                if (!is_array($h)) $h = json_decode((string)$h, true) ?? [];

                for ($d = 0; $d < $DAYS; $d++) {
                    for ($a = 0; $a < $SLOTS; $a++) {
                        $cell = (int) ($h[$d][$a] ?? 0);
                        $allowed = $cell === 1; // 1 = disponível; 2 = já tem aula
                        if ($allowed && $gridLabels[$a][$d] === '') {
                            $gridLabels[$a][$d] = $matName . ' — ' . $p->nome;
                            $gridIds[$a][$d] = [
                                'professor_id' => $p->id,
                                'materia_id'   => $p->materia->id ?? null,
                            ];
                            $toPlace--;
                            if ($toPlace === 0) break 2;
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

    private function buildGridHeuristic($professors, string $periodo, int $maxTries): array
    {
        $DAYS  = 5; $SLOTS = 5;
        $start = hrtime(true);

        $profs = is_array($professors) ? $professors : $professors->all();
        $need = [];
        $availMap = [];
        foreach ($profs as $p) {
            $pid = (int) $p->id;
            $need[$pid] = max(0, (int) ($p->materia->quant_aulas ?? 0));
            $h = $this->getProfGrid($p, $periodo);
            if (!is_array($h)) $h = json_decode((string)$h, true) ?? [];
            $cells = [];
            for ($d = 0; $d < $DAYS; $d++) {
                $row = $h[$d] ?? [];
                for ($a = 0; $a < $SLOTS; $a++) {
                    if ((int) ($row[$a] ?? 0) === 1) $cells[] = [$d, $a];
                }
            }
            $availMap[$pid] = $cells;
        }

        $best = null; $bestRemainingSum = PHP_INT_MAX; $iterations = 0;
        while ($iterations < $maxTries) {
            $iterations++;
            $gridLabels = array_fill(0, $SLOTS, array_fill(0, $DAYS, ''));
            $gridIds    = array_fill(0, $SLOTS, array_fill(0, $DAYS, null));
            $remaining  = $need;

            $order = $profs;
            usort($order, function ($a, $b) use ($availMap, $need) {
                $aid = (int) $a->id; $bid = (int) $b->id;
                $aAvail = max(1, count($availMap[$aid] ?? []));
                $bAvail = max(1, count($availMap[$bid] ?? []));
                $aNeed  = (int) ($need[$aid] ?? 0);
                $bNeed  = (int) ($need[$bid] ?? 0);
                $aScore = $aNeed / $aAvail;
                $bScore = $bNeed / $bAvail;
                if ($aScore === $bScore) {
                    if ($aAvail === $bAvail) return $bNeed <=> $aNeed;
                    return $aAvail <=> $bAvail;
                }
                return $bScore <=> $aScore;
            });
            if ($iterations > 1 && mt_rand(0,1)) shuffle($order);

            $dayLoad  = array_fill(0, $DAYS, 0);
            $slotLoad = array_fill(0, $SLOTS, 0);

            foreach ($order as $p) {
                $pid = (int) $p->id;
                $toPlace = (int) ($remaining[$pid] ?? 0);
                if ($toPlace <= 0) continue;
                $matName = $p->materia->nome ?? '—';

                $cells = $availMap[$pid] ?? [];
                usort($cells, function ($c1, $c2) use ($dayLoad, $slotLoad) {
                    [$d1, $a1] = $c1; [$d2, $a2] = $c2;
                    $w1 = $dayLoad[$d1] + 0.6 * $slotLoad[$a1];
                    $w2 = $dayLoad[$d2] + 0.6 * $slotLoad[$a2];
                    if ($w1 === $w2) return mt_rand(-1, 1);
                    return $w1 <=> $w2;
                });

                foreach ($cells as [$d, $a]) {
                    if ($toPlace <= 0) break;
                    if ($gridLabels[$a][$d] !== '') continue;
                    $gridLabels[$a][$d] = $matName . ' — ' . $p->nome;
                    $gridIds[$a][$d] = [
                        'professor_id' => $pid,
                        'materia_id'   => $p->materia->id ?? null,
                    ];
                    $toPlace--; $dayLoad[$d]++; $slotLoad[$a]++;
                }

                $remaining[$pid] = $toPlace;
            }

            $sum = array_sum($remaining);
            if ($sum < $bestRemainingSum) {
                $bestRemainingSum = $sum; $best = [$gridLabels, $gridIds, $remaining];
                if ($sum === 0) break;
            }
        }

        [$gridLabels, $gridIds, $remaining] = $best ?? [
            array_fill(0, $SLOTS, array_fill(0, $DAYS, '')),
            array_fill(0, $SLOTS, array_fill(0, $DAYS, null)),
            $need,
        ];

        $end = hrtime(true);
        $meta = [
            'iterations'  => $iterations,
            'duration_ms' => (int) (($end - $start) / 1e6),
            'remaining'   => $remaining,
        ];
        return [$gridLabels, $meta, $gridIds];
    }

    private function buildGridTimeBudget($professors, string $periodo, int $budgetMs): array
    {
        $DAYS  = 5; $SLOTS = 5;
        $start = hrtime(true);
        $deadline = $start + (int) ($budgetMs * 1e6);

        $profs = is_array($professors) ? $professors : $professors->all();
        $need = [];
        $availMap = [];
        foreach ($profs as $p) {
            $pid = (int) $p->id;
            $need[$pid] = max(0, (int) ($p->materia->quant_aulas ?? 0));
            $h = $this->getProfGrid($p, $periodo);
            if (!is_array($h)) $h = json_decode((string)$h, true) ?? [];
            $cells = [];
            for ($d = 0; $d < $DAYS; $d++) {
                $row = $h[$d] ?? [];
                for ($a = 0; $a < $SLOTS; $a++) {
                    if ((int) ($row[$a] ?? 0) === 1) $cells[] = [$d, $a];
                }
            }
            $availMap[$pid] = $cells;
        }

        $best = null; $bestRemainingSum = PHP_INT_MAX; $iterations = 0;
        while (hrtime(true) < $deadline) {
            $iterations++;
            $gridLabels = array_fill(0, $SLOTS, array_fill(0, $DAYS, ''));
            $gridIds    = array_fill(0, $SLOTS, array_fill(0, $DAYS, null));
            $remaining  = $need;

            // Randomiza ordem com leve viés pelos mais críticos
            $order = $profs;
            usort($order, function ($a, $b) use ($availMap, $need) {
                $aid = (int) $a->id; $bid = (int) $b->id;
                $aAvail = max(1, count($availMap[$aid] ?? []));
                $bAvail = max(1, count($availMap[$bid] ?? []));
                $aNeed  = (int) ($need[$aid] ?? 0);
                $bNeed  = (int) ($need[$bid] ?? 0);
                $aScore = $aNeed / $aAvail + mt_rand(0, 100) / 1000; // ruído
                $bScore = $bNeed / $bAvail + mt_rand(0, 100) / 1000;
                return $bScore <=> $aScore;
            });

            $dayLoad  = array_fill(0, $DAYS, 0);
            $slotLoad = array_fill(0, $SLOTS, 0);

            foreach ($order as $p) {
                $pid = (int) $p->id;
                $toPlace = (int) ($remaining[$pid] ?? 0);
                if ($toPlace <= 0) continue;
                $matName = $p->materia->nome ?? '—';

                $cells = $availMap[$pid] ?? [];
                shuffle($cells);
                usort($cells, function ($c1, $c2) use ($dayLoad, $slotLoad) {
                    [$d1, $a1] = $c1; [$d2, $a2] = $c2;
                    $w1 = $dayLoad[$d1] + 0.5 * $slotLoad[$a1];
                    $w2 = $dayLoad[$d2] + 0.5 * $slotLoad[$a2];
                    return $w1 <=> $w2;
                });

                foreach ($cells as [$d, $a]) {
                    if ($toPlace <= 0) break;
                    if ($gridLabels[$a][$d] !== '') continue;
                    $gridLabels[$a][$d] = $matName . ' — ' . $p->nome;
                    $gridIds[$a][$d] = [
                        'professor_id' => $pid,
                        'materia_id'   => $p->materia->id ?? null,
                    ];
                    $toPlace--; $dayLoad[$d]++; $slotLoad[$a]++;
                }

                $remaining[$pid] = $toPlace;
            }

            $sum = array_sum($remaining);
            if ($sum < $bestRemainingSum) {
                $bestRemainingSum = $sum; $best = [$gridLabels, $gridIds, $remaining];
                if ($sum === 0) break;
            }
        }

        [$gridLabels, $gridIds, $remaining] = $best ?? [
            array_fill(0, $SLOTS, array_fill(0, $DAYS, '')),
            array_fill(0, $SLOTS, array_fill(0, $DAYS, null)),
            $need,
        ];

        $end = hrtime(true);
        $meta = [
            'iterations'  => $iterations,
            'duration_ms' => (int) (($end - $start) / 1e6),
            'remaining'   => $remaining,
        ];
        return [$gridLabels, $meta, $gridIds];
    }

    public function saveGrid(Turma $turma, array $idsGrid, string $periodo): void
    {
        DB::transaction(function () use ($turma, $idsGrid, $periodo) {
            $DAYS = 5; $SLOTS = 5;

            $prev = $this->getTurmaGrid($turma, $periodo);
            if (!is_array($prev)) { $prev = json_decode((string) $prev, true) ?? []; }

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

            $toRestoreByProf = [];
            for ($a = 0; $a < $SLOTS; $a++) {
                $row = $prev[$a] ?? [];
                for ($d = 0; $d < $DAYS; $d++) {
                    $slot = $row[$d] ?? null;
                    if (is_array($slot) && !empty($slot['professor_id'])) {
                        $pid = (int) $slot['professor_id'];
                        $toRestoreByProf[$pid][] = [$d, $a];
                    }
                }
            }
            if ($toRestoreByProf) {
                $professores = Professor::whereIn('id', array_keys($toRestoreByProf))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');
                foreach ($toRestoreByProf as $pid => $coords) {
                    if (!isset($professores[$pid])) continue;
                    $h = $this->getProfGrid($professores[$pid], $periodo);
                    if (!is_array($h)) $h = json_decode((string) $h, true) ?? [];
                    for ($d = 0; $d < $DAYS; $d++) {
                        if (!isset($h[$d]) || !is_array($h[$d])) $h[$d] = array_fill(0, $SLOTS, 0);
                        $h[$d] = array_pad(array_slice($h[$d], 0, $SLOTS), $SLOTS, 0);
                    }
                    foreach ($coords as [$d, $a]) {
                        if (($h[$d][$a] ?? 0) === 2) {
                            $hasOther = !empty($stillAllocated[$pid][$d][$a]);
                            if (!$hasOther) {
                                $h[$d][$a] = 1;
                            }
                        }
                    }
                    $this->setProfGrid($professores[$pid], $periodo, $h);
                    $professores[$pid]->save();
                }
            }

            $this->setTurmaGrid($turma, $periodo, $idsGrid);
            $turma->save();

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
                $professores = Professor::whereIn('id', array_keys($byProf))
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');
                foreach ($byProf as $pid => $coords) {
                    if (!isset($professores[$pid])) continue;
                    $h = $this->getProfGrid($professores[$pid], $periodo);
                    if (!is_array($h)) $h = json_decode((string) $h, true) ?? [];
                    for ($d = 0; $d < $DAYS; $d++) {
                        if (!isset($h[$d]) || !is_array($h[$d])) $h[$d] = array_fill(0, $SLOTS, 0);
                        $h[$d] = array_pad(array_slice($h[$d], 0, $SLOTS), $SLOTS, 0);
                    }
                    foreach ($coords as [$d, $a]) {
                        $h[$d][$a] = 2; // aula
                    }
                    $this->setProfGrid($professores[$pid], $periodo, $h);
                    $professores[$pid]->save();
                }
            }
        });
    }

    public function clearTurma(Turma $turma, string $periodo): void
    {
        DB::transaction(function () use ($turma, $periodo) {
            $DAYS = 5; $SLOTS = 5;

            $prev = $this->getTurmaGrid($turma, $periodo);
            if (!is_array($prev)) { $prev = json_decode((string) $prev, true) ?? []; }

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

            $toRestoreByProf = [];
            for ($a = 0; $a < $SLOTS; $a++) {
                $row = $prev[$a] ?? [];
                for ($d = 0; $d < $DAYS; $d++) {
                    $slot = $row[$d] ?? null;
                    if (is_array($slot) && !empty($slot['professor_id'])) {
                        $pid = (int) $slot['professor_id'];
                        $toRestoreByProf[$pid][] = [$d, $a];
                    }
                }
            }
            if ($toRestoreByProf) {
                $professores = Professor::whereIn('id', array_keys($toRestoreByProf))->get()->keyBy('id');
                foreach ($toRestoreByProf as $pid => $coords) {
                    if (!isset($professores[$pid])) continue;
                    $h = $this->getProfGrid($professores[$pid], $periodo);
                    if (!is_array($h)) $h = json_decode((string) $h, true) ?? [];
                    for ($d = 0; $d < $DAYS; $d++) {
                        if (!isset($h[$d]) || !is_array($h[$d])) $h[$d] = array_fill(0, $SLOTS, 0);
                        $h[$d] = array_pad(array_slice($h[$d], 0, $SLOTS), $SLOTS, 0);
                    }
                    foreach ($coords as [$d, $a]) {
                        if (($h[$d][$a] ?? 0) === 2) {
                            $hasOther = !empty($stillAllocated[$pid][$d][$a]);
                            if (!$hasOther) {
                                $h[$d][$a] = 1;
                            }
                        }
                    }
                    $this->setProfGrid($professores[$pid], $periodo, $h);
                    $professores[$pid]->save();
                }
            }

            $this->setTurmaGrid($turma, $periodo, null);
            $turma->save();
        });
    }

    public function getProfGrid(Professor $p, string $periodo)
    {
        return match($periodo) {
            'manha' => $p->horario_manha,
            'tarde' => $p->horario_tarde,
            'noite' => $p->horario_noite,
        };
    }

    public function setProfGrid(Professor $p, string $periodo, $grid): void
    {
        match($periodo) {
            'manha' => $p->horario_manha = $grid,
            'tarde' => $p->horario_tarde = $grid,
            'noite' => $p->horario_noite = $grid,
        };
    }

    public function getTurmaGrid(Turma $t, string $periodo)
    {
        return match($periodo) {
            'manha' => $t->horario_manha,
            'tarde' => $t->horario_tarde,
            'noite' => $t->horario_noite,
        };
    }

    public function setTurmaGrid(Turma $t, string $periodo, $grid): void
    {
        match($periodo) {
            'manha' => $t->horario_manha = $grid,
            'tarde' => $t->horario_tarde = $grid,
            'noite' => $t->horario_noite = $grid,
        };
    }
}
