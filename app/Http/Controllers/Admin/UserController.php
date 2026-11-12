<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));

        $users = User::with('role')
            ->when($request->filled('role'), fn ($q) => $q->where('role_id', $request->integer('role')))
            ->when($search !== '', function ($q) use ($search) {
                $term = '%'.mb_strtolower($search).'%';
                $q->where(fn ($w) => $w
                    ->whereRaw('LOWER(first_name) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(last_name) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(email) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(company_name) LIKE ?', [$term])
                    ->orWhere('ruc', 'like', $search.'%'));
            })
            ->orderBy('first_name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.form', [
            'user' => new User(['role_id' => Role::idFor(Role::CLIENT)]),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $user = User::create($request->validated());

        return redirect()->route('admin.users.index')
            ->with('success', "Usuario {$user->full_name} creado correctamente.");
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', [
            'user' => $user,
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        // Un administrador no puede cambiar su propio rol: se quedaría sin acceso al panel.
        if ($user->is($request->user()) && (int) $data['role_id'] !== $user->role_id) {
            return back()->withInput()->with('error', 'No puedes cambiar tu propio rol.');
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', "Usuario {$user->full_name} actualizado.");
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        // La base no deja borrar un usuario con importaciones o eventos (claves foráneas con restrict).
        if ($user->clientImports()->exists() || $user->operatedImports()->exists() || $user->trackingEvents()->exists()) {
            return back()->with('error', "No se puede eliminar a {$user->full_name}: tiene importaciones o eventos asociados.");
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Usuario {$user->full_name} eliminado.");
    }
}
