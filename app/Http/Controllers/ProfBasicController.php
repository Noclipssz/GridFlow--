<?php

namespace App\Http\Controllers;

use App\Models\Professor;
use App\Models\Turma;
use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProfBasicController extends Controller
{
    public function showLogin()
    {
        return view('prof.basic.login');
    }

    public function doLogin(Request $request)
    {
        $request->validate([
            'cpf'   => ['required', 'string', 'min:11', 'max:14'],
            'senha' => ['required', 'string', 'min:6'],
        ]);

        $cpfDigits = preg_replace('/\D+/', '', $request->input('cpf'));

        $prof = Professor::where('cpf', $cpfDigits)->first();

        if (!$prof || !Hash::check($request->input('senha'), (string) $prof->senha)) {
            return back()->withErrors(['cpf' => 'CPF ou senha inválidos.'])
                ->withInput(['cpf' => $request->input('cpf')]);
        }

        // Login "manual": grava só o id na sessão
        $request->session()->regenerate(); // previne fixation
        $request->session()->put('prof_id', $prof->id);

        return redirect()->route('prof.basic.dashboard');
    }

    public function listTurmas(Request $request)
    {
        $id = $request->session()->get('prof_id');
        if (!$id) return redirect()->route('prof.basic.login');

        $prof = Professor::with('materia')->findOrFail($id);

        // Busca turmas onde o professor aparece em pelo menos um slot
        $turmas = Turma::orderBy('id')->get();
        $mine = [];
        foreach ($turmas as $t) {
            $count = 0;
            foreach (['manha','tarde','noite'] as $per) {
                $grid = match($per) {
                    'manha' => $t->horario_manha,
                    'tarde' => $t->horario_tarde,
                    'noite' => $t->horario_noite,
                };
                if (!is_array($grid)) $grid = json_decode((string) $grid, true) ?? [];
                for ($a = 0; $a < 5; $a++) {
                    $row = $grid[$a] ?? [];
                    for ($d = 0; $d < 5; $d++) {
                        $cell = $row[$d] ?? null;
                        if (is_array($cell) && (int)($cell['professor_id'] ?? 0) === $prof->id) {
                            $count++;
                        }
                    }
                }
            }
            if ($count > 0) {
                $mine[] = [
                    'turma' => $t,
                    'aulas' => $count,
                ];
            }
        }

        return view('prof.turmas.index', [
            'prof' => $prof,
            'mine' => $mine,
        ]);
    }

    public function showTurma(Request $request, Turma $turma)
    {
        $id = $request->session()->get('prof_id');
        if (!$id) return redirect()->route('prof.basic.login');

        $prof = Professor::with('materia')->findOrFail($id);

        // Garantir que este professor participa desta turma
        $periodo = $request->input('periodo', 'manha');
        $grid = match($periodo) {
            'manha' => $turma->horario_manha,
            'tarde' => $turma->horario_tarde,
            'noite' => $turma->horario_noite,
        };
        if (!is_array($grid)) $grid = json_decode((string) $grid, true) ?? [];
        $isMember = false;
        for ($a = 0; $a < 5; $a++) {
            $row = $grid[$a] ?? [];
            for ($d = 0; $d < 5; $d++) {
                $cell = $row[$d] ?? null;
                if (is_array($cell) && (int)($cell['professor_id'] ?? 0) === $prof->id) {
                    $isMember = true; break 2;
                }
            }
        }
        if (!$isMember) {
            return redirect()->route('prof.turmas.index')->withErrors(['turma' => 'Você não possui aulas nesta turma.']);
        }

        // Coletar IDs usados para resolver nomes
        $profIds = [];
        $matIds  = [];
        for ($a = 0; $a < 5; $a++) {
            $row = $grid[$a] ?? [];
            for ($d = 0; $d < 5; $d++) {
                $cell = $row[$d] ?? null;
                if (is_array($cell)) {
                    if (!empty($cell['professor_id'])) $profIds[] = (int) $cell['professor_id'];
                    if (!empty($cell['materia_id']))   $matIds[]  = (int) $cell['materia_id'];
                }
            }
        }
        $profMap = Professor::whereIn('id', array_unique($profIds))->get()->keyBy('id');
        $matMap  = Materia::whereIn('id', array_unique($matIds))->get()->keyBy('id');

        $days = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta'];

        // Determina os períodos que têm aulas para esta turma
        $periodosAtivos = [];
        foreach (['manha', 'tarde', 'noite'] as $per) {
            $horario = $turma->{'horario_' . $per};
            if (!is_array($horario)) {
                $horario = json_decode((string) $horario, true) ?? [];
            }
            // Verifica se a grade não está vazia
            if (!empty(array_filter(array_merge(...$horario)))) {
                $periodosAtivos[] = $per;
            }
        }

        return view('prof.turmas.show', [
            'prof'   => $prof,
            'turma'  => $turma,
            'grid'   => $grid,
            'profMap'=> $profMap,
            'matMap' => $matMap,
            'days'   => $days,
            'periodo'=> $periodo,
            'periodosAtivos' => $periodosAtivos,
        ]);
    }

    public function showRegister()
    {
        return view('prof.basic.register');
    }

    public function doRegister(Request $request)
    {
        $data = $request->validate([
            'nome'       => ['required', 'string', 'max:255'],
            'cpf'        => ['required', 'string', 'max:14'],
            'senha'      => ['required', 'string', 'min:6', 'confirmed'],
            'materia_id' => ['nullable', 'integer', 'exists:materias,id'],
        ]);

        $cpfDigits = preg_replace('/\D+/', '', $data['cpf']);

        // unicidade do CPF (char(11))
        $request->validate([
            'cpf' => [Rule::unique('professores', 'cpf')],
        ]);

        $prof = Professor::create([
            'nome'       => $data['nome'],
            'cpf'        => $cpfDigits,
            'senha'      => Hash::make($data['senha']), // hash explícito
            'materia_id' => $data['materia_id'] ?? null,
        ]);

        // “autologin” simples
        $request->session()->regenerate();
        $request->session()->put('prof_id', $prof->id);

        return redirect()->route('prof.basic.dashboard');
    }

    public function dashboard(Request $request)
    {
        $id = $request->session()->get('prof_id');
        if (!$id) {
            return redirect()->route('prof.basic.login');
        }

        $prof = Professor::with('materia')->find($id);
        if (!$prof) {
            // sessão órfã
            $this->logout($request);
            return redirect()->route('prof.basic.login');
        }

        // CONTAGEM DE TURMAS ATIVAS
        // Reutiliza a lógica de `listTurmas` para consistência
        $turmas = Turma::orderBy('id')->get();
        $turmasAtivas = 0;
        foreach ($turmas as $t) {
            $isMember = false;
            foreach (['manha','tarde','noite'] as $per) {
                $grid = $t->{'horario_' . $per};
                if (!is_array($grid)) $grid = json_decode((string) $grid, true) ?? [];

                for ($a = 0; $a < 5; $a++) {
                    $row = $grid[$a] ?? [];
                    for ($d = 0; $d < 5; $d++) {
                        $cell = $row[$d] ?? null;
                        if (is_array($cell) && (int)($cell['professor_id'] ?? 0) === $prof->id) {
                            $isMember = true;
                            break 3; // Sai de todos os loops aninhados
                        }
                    }
                }
            }
            if ($isMember) {
                $turmasAtivas++;
            }
        }

        return view('prof.basic.dashboard', compact('prof', 'turmasAtivas'));
    }

    public function logout(Request $request)
    {
        $request->session()->forget('prof_id');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('prof.basic.login');
    }

    public function showSchedule(Request $request)
    {
        $id = $request->session()->get('prof_id');
        if (!$id) return redirect()->route('prof.basic.login');

        $prof = Professor::findOrFail($id);

        $periodo = $request->input('periodo', 'manha');

        // Config da grade (ajuste se quiser 5x5, 6x4, etc.)
        $rows = 5; // 1ª..6ª aula
        $cols = 5; // Segunda..Sexta
        $days = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta'];

        // Seleciona a grade do período no banco [dia][aula]
        $gridFromDb = match($periodo) {
            'manha' => $prof->horario_manha,
            'tarde' => $prof->horario_tarde,
            'noite' => $prof->horario_noite,
        };
        Log::info('showSchedule: raw grid from DB', ['grid' => $gridFromDb]);

        if (!is_array($gridFromDb)) $gridFromDb = json_decode((string) $gridFromDb, true) ?? [];

        // Transpose for display: [day][class] -> [class][day]
        $gridForDisplay = $this->transpose($gridFromDb);

        // pad/truncate for display dimensions (aula x dia), preservando estados 0/1/2
        $norm = [];
        for ($i = 0; $i < $rows; $i++) { // $rows is for display (aula)
            $row = $gridForDisplay[$i] ?? [];
            $row = array_map(function ($v) { $v = (int) $v; return in_array($v, [0,1,2], true) ? $v : (int) !!$v; }, array_slice((array)$row, 0, $cols));
            $row = array_pad($row, $cols, 0);
            $norm[] = $row;
        }

        Log::info('showSchedule: normalized grid for display', ['norm' => $norm]);

        return view('prof.basic.schedule', [
            'prof' => $prof,
            'rows' => $rows,
            'cols' => $cols,
            'days' => $days,
            'grid' => $norm, // This is [class][day]
            'periodo' => $periodo,
        ]);
    }

    public function saveSchedule(Request $request)
    {
        $id = $request->session()->get('prof_id');
        $periodo = $request->input('periodo', 'manha');

        Log::info('schedule.save: hit', [
            'prof_id' => $id,
            // cuidado com payload grande — logue só o tamanho:
            'grid_len' => strlen((string) $request->input('grid')),
            'route' => 'prof.basic.schedule.save',
        ]);

        if (!$id) {
            Log::warning('schedule.save: no session prof_id');
            return redirect()->route('prof.basic.login');
        }

        try {
            $request->validate(['grid' => ['required', 'json']]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('schedule.save: validation failed', ['errors' => $e->errors()]);
            throw $e;
        }

        $raw = (string) $request->input('grid');
        $gridFromFrontend = json_decode($raw, true); // This is [class][day]
        Log::info('schedule.save: decoded', [
            'json_error' => json_last_error_msg(),
            'decoded_type' => gettype($gridFromFrontend),
            'rows' => is_array($gridFromFrontend) ? count($gridFromFrontend) : null,
        ]);

        // Transpose for saving: [class][day] -> [day][class]
        $postedGrid = $this->transpose((array) $gridFromFrontend);

        // Salvar com comparação antes/depois
        DB::transaction(function () use ($id, $postedGrid, $periodo) {
            /** @var \App\Models\Professor $prof */
            $prof = \App\Models\Professor::lockForUpdate()->findOrFail($id);

            Log::info('schedule.save: BEFORE', [
                'id' => $prof->id,
                'periodo' => $periodo,
            ]);

            // Mescla mantendo 2 (aula). Onde não for 2, aceita 0/1 do payload.
            $current = match($periodo) {
                'manha' => $prof->horario_manha,
                'tarde' => $prof->horario_tarde,
                'noite' => $prof->horario_noite,
            };
            if (!is_array($current)) $current = json_decode((string) $current, true) ?? [];

            $DAYS = 5; $SLOTS = 5;
            $merged = [];
            for ($d = 0; $d < $DAYS; $d++) {
                $merged[$d] = [];
                for ($a = 0; $a < $SLOTS; $a++) {
                    $cur = (int) ($current[$d][$a] ?? 0);
                    $new = (int) ($postedGrid[$d][$a] ?? 0);
                    // normaliza new para 0/1
                    $new = $new === 1 ? 1 : 0;
                    $merged[$d][$a] = ($cur === 2) ? 2 : $new;
                }
            }

            match($periodo) {
                'manha' => $prof->horario_manha = $merged,
                'tarde' => $prof->horario_tarde = $merged,
                'noite' => $prof->horario_noite = $merged,
            }; // (cast array->json)

            Log::info('schedule.save: isDirty', ['isDirty' => $prof->isDirty()]);
            Log::info('schedule.save: getDirty', ['getDirty' => $prof->getDirty()]);

            $result = $prof->save();

            Log::info('schedule.save: save result', ['result' => $result]);

            $prof->refresh();

            Log::info('schedule.save: AFTER', [
                'id' => $prof->id,
                'periodo' => $periodo,
            ]);
        });

        return redirect()->route('prof.basic.schedule', ['periodo' => $periodo])->with('ok', 'Disponibilidades salvas!');
    }

    private function transpose(array $grid): array
    {
        $transposed = [];
        if (empty($grid)) {
            return $transposed;
        }

        $numRows = count($grid);
        $numCols = count($grid[0]);

        for ($c = 0; $c < $numCols; $c++) {
            $transposedRow = [];
            for ($r = 0; $r < $numRows; $r++) {
                $transposedRow[] = $grid[$r][$c];
            }
            $transposed[] = $transposedRow;
        }
        return $transposed;
    }
}
