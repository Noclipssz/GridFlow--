<?php

namespace App\Http\Controllers;

use App\Models\Professor;
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

        return view('prof.basic.dashboard', compact('prof'));
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

        // Config da grade (ajuste se quiser 5x5, 6x4, etc.)
        $rows = 5; // 1ª..6ª aula
        $cols = 5; // Segunda..Sexta
        $days = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta'];

        // Normaliza o horario_dp do banco para o tamanho escolhido
        $gridFromDb = $prof->horario_dp; // This is [day][class]
        Log::info('showSchedule: raw grid from DB', ['grid' => $gridFromDb]);

        if (!is_array($gridFromDb)) $gridFromDb = json_decode((string) $gridFromDb, true) ?? [];

        // Transpose for display: [day][class] -> [class][day]
        $gridForDisplay = $this->transpose($gridFromDb);

        // pad/truncate for display dimensions (aula x dia)
        $norm = [];
        for ($i = 0; $i < $rows; $i++) { // $rows is for display (aula)
            $row = $gridForDisplay[$i] ?? [];
            $row = array_map(fn($v) => (int)!!$v, array_slice($row, 0, $cols)); // $cols is for display (dia)
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
        ]);
    }

    public function saveSchedule(Request $request)
    {
        $id = $request->session()->get('prof_id');

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
        $gridForDb = $this->transpose($gridFromFrontend);

        // Sanitiza para 0/1 (apply to the transposed grid)
        $gridForDb = array_map(
            fn($row) => array_map(fn($v) => (int) !!$v, (array) $row),
            (array) $gridForDb
        );

        // Salvar com comparação antes/depois
        DB::transaction(function () use ($id, $gridForDb) {
            /** @var \App\Models\Professor $prof */
            $prof = \App\Models\Professor::lockForUpdate()->findOrFail($id);

            Log::info('schedule.save: BEFORE', [
                'id' => $prof->id,
                'raw' => $prof->getRawOriginal('horario_dp'),
                'cast_type' => gettype($prof->horario_dp),
                'cast_preview' => is_array($prof->horario_dp) ? $prof->horario_dp : null,
            ]);

            $prof->horario_dp = $gridForDb; // (cast array->json)

            Log::info('schedule.save: isDirty', ['isDirty' => $prof->isDirty()]);
            Log::info('schedule.save: getDirty', ['getDirty' => $prof->getDirty()]);

            $result = $prof->save();

            Log::info('schedule.save: save result', ['result' => $result]);

            $prof->refresh();

            Log::info('schedule.save: AFTER', [
                'id' => $prof->id,
                'raw' => $prof->getRawOriginal('horario_dp'),
                'cast_type' => gettype($prof->horario_dp),
                'cast_preview' => is_array($prof->horario_dp) ? $prof->horario_dp : null,
            ]);
        });

        return redirect()->route('prof.basic.schedule')->with('ok', 'Disponibilidades salvas!');
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
