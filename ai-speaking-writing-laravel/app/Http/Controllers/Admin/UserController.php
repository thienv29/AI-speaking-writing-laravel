<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereNull('deleted_at');

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $users = $query->withCount('attempts')
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->loadCount(['attempts']);
        $user->load(['attempts' => function($q) {
            $q->with(['question.exercises.type', 'question.exercises.lesson'])
              ->orderBy('created_at', 'desc')
              ->limit(10);
        }]);

        return view('admin.users.show', compact('user'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'dob' => 'nullable|date',
            'phone_number' => 'nullable|string|max:15|unique:users,phone_number',
            'role' => 'nullable|string|in:user,admin',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['role'] = $validated['role'] ?? 'user';

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Người dùng đã được tạo thành công!');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'dob' => 'nullable|date',
            'phone_number' => 'nullable|string|max:15|unique:users,phone_number,' . $user->id,
            'role' => 'nullable|string|in:user,admin',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Người dùng đã được cập nhật thành công!');
    }

    public function destroy(User $user)
    {
        if ($user->attempts()->exists()) {
            $user->delete();
            $message = 'Người dùng đã được xóa (soft delete) vì có liên kết với attempts.';
        } else {
            $user->forceDelete();
            $message = 'Người dùng đã được xóa vĩnh viễn.';
        }

        return redirect()->route('admin.users.index')
            ->with('success', $message);
    }
}

