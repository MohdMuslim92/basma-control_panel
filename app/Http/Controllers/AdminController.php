<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\User;
use Carbon\Carbon;
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

    /**
     * Toggle the admin status of a user.
     *
     * @param int $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleAdmin($userId)
    {
        try {
            // Find the admin record by user_id
            $admin = Admin::where('user_id', $userId)->firstOrFail();

            // Update the ended_at field to the current time only when admin is 1 to indicate the time the admin left
            if ($admin->admin == 1) {
                $admin->ended_at = Carbon::now();
            }
            
            // Toggle the admin status
            $admin->admin = $admin->admin ? 0 : 1;
            $admin->save();

            return response()->json(['message' => 'Admin status updated successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating admin status'], 500);
        }
    }
}
