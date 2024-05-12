<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index()
    {
        // Fetch the Membership officers from the database with user data
        $officers = Admin::with('user')->where('office_id', 2)->get();
        
        // Iterate through each officer and fetch their related users
        $officers->each(function ($officer) {
            $officer->users = User::where('admin_mail', $officer->user->email)->get();
        });

        return response()->json($officers);
    }
    
    public function showAdminsList()
    {
        return Inertia::render('Admins');
    }

}
