<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SystemUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->has('role') && $request->role != 'all') {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('username', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->paginate(15)->withQueryString();
        $roles = Role::all();
        $clinics = \App\Models\Clinic::all();

        return view('users.index', compact('users', 'roles', 'clinics'));
    }

    public function show($id)
    {
        $user = User::with('roles', 'permissions')->findOrFail($id);
        $activities = \Spatie\Activitylog\Models\Activity::where('causer_id', $id)
                        ->latest()
                        ->take(50)
                        ->get();
        $allPermissions = \Spatie\Permission\Models\Permission::all()->groupBy(function($permission) {
            return explode('_', $permission->name)[1] ?? 'general';
        });
        
        return view('users.show', compact('user', 'activities', 'allPermissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string',
            'roles' => 'required|array',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'clinic_id' => 'nullable|exists:clinics,id',
            'specialization' => 'nullable|string',
            'license_number' => 'nullable|string',
            'consultation_duration' => 'nullable|integer|min:5'
        ]);

        $data = $request->except(['password', 'roles', 'avatar']);
        $data['password'] = Hash::make($request->password);
        $data['status'] = 'نشط';
        
        // Legacy fallback
        $data['role'] = $request->roles[0] ?? 'User';

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user = User::create($data);
        $user->syncRoles($request->roles);

        return back()->with('success', 'تم إضافة الموظف بنجاح وتعيين الصلاحيات.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->hasRole('Super Admin') && $user->id !== auth()->id() && !auth()->user()->hasRole('Super Admin')) {
            return back()->withErrors(['error' => 'حماية أمنية: لا تملك الصلاحية لتعديل بيانات مدير عام النظام.']);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'username' => 'required|string|unique:users,username,' . $id,
            'phone' => 'nullable|string',
            'password' => 'nullable|string|min:8',
            'roles' => 'required|array',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'clinic_id' => 'nullable|exists:clinics,id',
            'specialization' => 'nullable|string',
            'license_number' => 'nullable|string',
            'consultation_duration' => 'nullable|integer|min:5'
        ]);

        $data = $request->except(['password', 'roles', 'avatar']);
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Legacy fallback
        $data['role'] = $request->roles[0] ?? 'User';

        $user->update($data);
        $user->syncRoles($request->roles);

        return back()->with('success', 'تم تحديث بيانات الموظف بنجاح.');
    }

    public function updatePermissions(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($user->hasRole("Super Admin")) return back()->withErrors(["error" => "حماية أمنية: مدير النظام يمتلك كافة الصلاحيات."]);
        
        if ($request->has('permissions')) {
            $user->syncPermissions($request->permissions);
        } else {
            $user->syncPermissions([]);
        }

        return back()->with('success', 'تم تحديث الصلاحيات الاستثنائية للموظف بنجاح.');
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        if ($user->hasRole("Super Admin")) return back()->withErrors(["error" => "حماية أمنية: يُمنع إيقاف حساب يمتلك دور (مدير النظام 👑)."]);
        
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'لا يمكنك إيقاف حسابك الشخصي.']);
        }

        $user->update([
            'status' => $user->status === 'نشط' ? 'موقوف' : 'نشط'
        ]);

        return back()->with('success', 'تم تغيير حالة الحساب بنجاح.');
    }
}
