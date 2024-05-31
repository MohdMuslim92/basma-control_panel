<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use \App\Models\User;
use App\Models\Admin;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function showUserList()
    {
        return Inertia::render('UserList');
    }

    public function index(Request $request)
    {
        // Fetch users and join with the admins table to get the admin status
        $users = DB::table('users')
        ->leftJoin('admins', 'users.id', '=', 'admins.user_id')
        ->select('users.*', 'admins.admin as is_admin')
        ->paginate(10);

        return response()->json($users);
    }

    public function update(Request $request, $id)
    {
        // Find the user by ID
        $user = User::findOrFail($id);
        
        // Validate the request data
        $validatedData = $request->validate([
            // Define validation rules for each field under update
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'string', 'max:6'],
            'state_id' => ['required', 'numeric'],
            'province_id' => ['required', 'numeric'],
            'address' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'dob' => ['required', 'date'],
            'educationLevel' => ['required', Rule::in(['Non_formal_education', 'Elementary', 'Secondary', 'University'])],
            'specialization' => ['nullable', 'string'],
            'skills' => ['required', 'string'],
            'alreadyVolunteering' => ['required', Rule::in(['true', 'false'])],
            'organizationName' => ['nullable', 'string'],
            'volunteeringStartDay' => ['nullable', 'date'],
            'volunteeringEndDay' => ['nullable', 'date'],
            'monthlyShare' => ['required', 'numeric'],
            'meetingDay' => ['required', 'string'],
        ]);
        
        // Update the user's information based on the validated data
        $user->update($validatedData);

        // Return a response indicating success
        return response()->json(['message' => 'User information updated successfully']);

    }

    public function displayUserDetails($id)
    {
        $user = User::findOrFail($id); // Fetch the user from the database by ID
        return Inertia::render('UserDetails', ['user' => $user]); // Pass the user data to the view
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

     // Method to fetch users related to the admin
     public function getUsersByAdmin(Request $request)
     {
         // the admin is authenticated and their ID is accessible via $request->user()->id
         $adminMail = $request->user()->email;
 
         // Fetch users related to the admin
         $users = User::where('admin_mail', $adminMail)->get();

         return response()->json($users);
     }

     public function getAddAndLeaveReport(Request $request)
     {
        $month = $request->query('month');
        $year = $request->query('year');

        if (!$month || !$year) {
            return response()->json([
                'error' => 'Month and Year are required'
            ], 400);
        }

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        $leftUsers = User::whereIn('user_status_id', [3, 5, 11])
            ->whereBetween('gone_at', [$startDate, $endDate])
            ->get(['id', 'name']);

        $addedUsers = User::where('user_status_id', '!=', 1)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get(['id', 'name']);

        return response()->json([
            'addedUsers' => $addedUsers,
            'leftUsers' => $leftUsers
        ]);
    }

    /**
     * Delete a user by updating their status and setting the gone_at column.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUser(Request $request, $id)
    {
        $request->validate([
            'status_id' => 'required|integer',
        ]);

        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->user_status_id = $request->input('status_id');
        $user->gone_at = Carbon::now();
        $user->save();

        return response()->json(['message' => 'User deleted successfully']);
    }

    public function getCurrentUser(Request $request)
    {
        $userId = Auth::id();

        if ($userId) {
            $user = DB::table('users')
                ->leftJoin('admins', 'users.id', '=', 'admins.user_id')
                ->select('users.*', 'admins.admin as is_admin')
                ->where('users.id', $userId)
                ->first();

            return response()->json($user);
        } else {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
    }
}
