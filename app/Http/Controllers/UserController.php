<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = \App\Models\User::orderBy('id', 'desc')->paginate(15);
        return view('users.index', compact('users'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'role' => 'required|string',
        ]);

        \App\Models\User::create([
            'username' => $request->username,
            'name' => $request->name,
            'email' => $request->username . '@local.system', // dummy email
            'password' => bcrypt($request->password),
            'role' => $request->role,
            'status' => 'نشط',
        ]);

        return back()->with('success', 'تم إنشاء المستخدم بنجاح');
    }
}
