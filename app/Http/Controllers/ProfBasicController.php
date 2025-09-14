<?php

namespace App\Http\Controllers;

use App\Models\Professor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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

}
