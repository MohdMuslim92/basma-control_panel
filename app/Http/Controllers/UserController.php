<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\User;
use App\Models\Admin;
use Inertia\Inertia;


class UserController extends Controller
{
    public function showUserList()
    {
        return Inertia::render('UserList');
    }

    public function index(Request $request)
    {
        $users = User::paginate(10); // Fetch users with pagination (10 per page)

        return response()->json($users); // Return users as JSON

    }

    public function updateRole(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->role_id = $request->role_id;
        $user->save();

        // Check if the user is already an admin for the given office
        $admin = Admin::where('user_id', $user->id)
        ->where('office_id', $request->role_id)
        ->first();

    if (!$admin) {
        // If the user is not an admin for the office, create a new record
        $admin = new Admin();
        $admin->user_id = $user->id;
        $admin->office_id = $request->role_id;
        $admin->save();
    }

        return response()->json($user);
    }
}
