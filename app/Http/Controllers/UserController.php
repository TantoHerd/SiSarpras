<?php
// app/Http/Controllers/UserController.php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    /**
     * Daftar semua user
     */
    public function index(Request $request)
    {
        $filters = [
            'search'    => $request->input('search'),
            'role_id'   => $request->input('role_id'),
            'is_active' => $request->input('is_active'),
        ];

        $users = $this->userService->getUsers($filters, 15);
        $roles = Role::orderBy('id')->get();
        $stats = $this->userService->getStats();

        return view('users.index', compact('users', 'roles', 'stats', 'filters'));
    }

    /**
     * Form tambah user
     */
    public function create()
    {
        $roles = Role::orderBy('id')->get();
        return view('users.create', compact('roles'));
    }

    /**
     * Simpan user baru
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $result = $this->userService->createUser($request->validated());

            return redirect()
                ->route('users.index')
                ->with('success', "User \"{$result['user']->name}\" berhasil dibuat.")
                ->with('new_user_password', [
                    'name'     => $result['user']->name,
                    'email'    => $result['user']->email,
                    'password' => $result['plain_password'],
                ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan user: ' . $e->getMessage());
        }
    }

    /**
     * Detail user
     */
    public function show(User $user)
    {
        $user->load('role');
        
        // Ambil riwayat aktivitas (dari loans sebagai contoh)
        $recentLoans = $user->loans()
            ->with('item')
            ->latest()
            ->take(5)
            ->get();

        return view('users.show', compact('user', 'recentLoans'));
    }

    /**
     * Form edit user
     */
    public function edit(User $user)
    {
        $roles = Role::orderBy('id')->get();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update user
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        try {
            $this->userService->updateUser($user, $request->validated());

            return redirect()
                ->route('users.index')
                ->with('success', "User \"{$user->name}\" berhasil diperbarui.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui user: ' . $e->getMessage());
        }
    }

    /**
     * Reset password user
     */
    public function resetPassword(User $user)
    {
        try {
            $result = $this->userService->resetPassword($user);

            return redirect()
                ->route('users.index')
                ->with('success', "Password user \"{$user->name}\" berhasil direset.")
                ->with('new_user_password', [
                    'name'     => $result['user']->name,
                    'email'    => $result['user']->email,
                    'password' => $result['plain_password'],
                ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal reset password: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status aktif user
     */
    public function toggleActive(User $user)
    {
        try {
            // Cek tidak bisa nonaktifkan diri sendiri
            if ($user->id === Auth::id()) {
                return back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
            }

            $this->userService->toggleActive($user);
            $status = $user->fresh()->is_active ? 'diaktifkan' : 'dinonaktifkan';

            return redirect()
                ->route('users.index')
                ->with('success', "User \"{$user->name}\" berhasil {$status}.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    /**
     * Hapus user (soft delete)
     */
    public function destroy(User $user)
    {
        try {
            $name = $user->name;
            $this->userService->deleteUser($user);

            return redirect()
                ->route('users.index')
                ->with('success', "User \"{$name}\" berhasil dihapus.");
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }
}