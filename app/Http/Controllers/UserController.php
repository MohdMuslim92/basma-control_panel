<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\User;
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
}
