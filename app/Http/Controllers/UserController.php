<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
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

    public function updateOfficer(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->admin_mail = $request->officerMail;
        $user->save();

        return response()->json($user);
    }


    public function approval($userId)
    {
        try {
            // Retrieve the user data from the database
            $user = User::where('user_status_id', 1)->findOrFail($userId);

            // Pass the user data to the view
            return inertia::render('UserApproval', [
                'userData' => $user ? $user->toArray() : null,
            ]);

        } catch (ModelNotFoundException $e) {
            // Handle the case where the user is not found
            return inertia::render('UserApprovalMessage', [
                'message' => 'User Already Approved.',
            ]);
        }
    }

    public function approveUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            // Update all user data with the form data
            $user->update($request->all());
            // Set user_status_id to 2 for active status
            $user->update(['user_status_id' => 2]);
            return response()->json(['message' => 'User approved successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to approve user'], 500);
        }
    }

    public function rejectUser(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            // Update all user data with the form data
            $user->update($request->all());
            // Set user_status_id to 9 for reject status
            $user->update(['user_status_id' => 9]);
            return response()->json(['message' => 'User rejected successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to reject user'], 500);
        }
    }
}
