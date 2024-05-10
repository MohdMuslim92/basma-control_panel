<?php

namespace App\Http\Controllers;

use App\Models\Admin;

class AdminController extends Controller
{
    public function index()
    {
        // Fetch the Membership officers from the database with user data
        $officers = Admin::with('user')->where('office_id', 2)->get();

        return response()->json($officers);
    }
}
