<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Obra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->where('role', '!=', 'chefe');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->with('obras')->latest()->paginate(10)->withQueryString();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $obras = Obra::where('status', '!=', 'concluida')->latest()->get();
        return view('users.create', compact('obras'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:operador,cliente',
            'obras' => 'nullable|array',
            'obras.*' => 'exists:obras,id',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        if (isset($validated['obras'])) {
            $user->obras()->sync($validated['obras']);
        }

        return redirect()->route('users.index')->with('success', 'Usuário criado com sucesso!');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        if ($user->role === 'chefe') {
            return redirect()->route('users.index')->with('error', 'Chefes de obra não podem ser gerenciados por aqui.');
        }

        $obras = Obra::where('status', '!=', 'concluida')->latest()->get();
        return view('users.edit', compact('user', 'obras'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->role === 'chefe') {
            return redirect()->route('users.index')->with('error', 'Chefes de obra não podem ser editados por aqui.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:operador,cliente',
            'obras' => 'nullable|array',
            'obras.*' => 'exists:obras,id',
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->obras()->sync($validated['obras'] ?? []);

        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        if ($user->role === 'chefe') {
            return redirect()->route('users.index')->with('error', 'Chefes de obra não podem ser removidos.');
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuário excluído com sucesso!');
    }
}
