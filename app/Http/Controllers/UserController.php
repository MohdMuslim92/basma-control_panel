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
    /**
     * Render the user list page using Inertia.js.
     *
     * This method retrieves and renders the user list page using Inertia.js,
     * which allows for dynamic, client-side rendering with Vue.js components.
     *
     * @return \Inertia\Response
    */
    public function showUserList()
    {
        return Inertia::render('UserList');
    }

    /**
     * Retrieve and paginate active users with admin status information.
     *
     * This method retrieves active users from the database, joining with the admins
     * table to include admin status and admin names where applicable. It paginates
     * the results to limit response size and improve performance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
    */
    public function index(Request $request)
    {
        try {
            // Fetch users and join with the admins table to get the admin status and admin name
            $users = DB::table('users')
            ->leftJoin('admins', 'users.id', '=', 'admins.user_id')
            ->leftJoin('users as admin_users', 'users.admin_mail', '=', 'admin_users.email')
            ->select('users.*', 'admins.admin as is_admin', 'admin_users.name as admin_name')
            ->whereIn('users.user_status_id', [2, 6, 7, 8]) // Filter active users
            ->paginate(10);

            return response()->json($users);
        } catch (\Exception $e) {
            // Return a generic error message
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Fetch all users data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllUsers(Request $request)
    {
        try {
            $users = User::whereIn('user_status_id', [2, 6, 7, 8])->get(); // Filter active users
            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch users data'], 500);
        }
    }

    /**
     * Update user information.
     *
     * This method updates the information of a specific user identified by the given ID
     * using the validated data from the request. It performs data validation against
     * specified rules for each field and then updates the user's information accordingly.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  The ID of the user to update
     * @return \Illuminate\Http\JsonResponse
    */
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

    /**
     * Display details of a specific user.
     *
     * Fetches the user information from the database based on the provided ID,
     * then renders the 'UserDetails' Inertia.js view, passing the user data to it.
     *
     * @param  int  $id  The ID of the user to display details for
     * @return \Inertia\Response
     */
    public function displayUserDetails($id)
    {
        $user = User::findOrFail($id); // Fetch the user from the database by ID
        return Inertia::render('UserDetails', ['user' => $user]); // Pass the user data to the view
    }

    /**
     * Update the role of a user.
     *
     * Finds the user by the provided ID, updates their role ID based on the request data,
     * and saves the changes. Checks if the user is already an admin for the given office
     * and creates a new Admin record if not.
     *
     * @param  \Illuminate\Http\Request  $request  The HTTP request containing the new role ID
     * @param  int  $id  The ID of the user whose role is to be updated
     * @return \Illuminate\Http\JsonResponse  JSON response containing the updated user information
    */
    public function updateRole(Request $request, $id)
    {
        // Find user by ID
        $user = User::findOrFail($id);

        // Update the user's role ID based on the request data
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

    /**
     * Update the officer (admin) assigned to a user for follow-up.
     *
     * Finds the user by the provided ID, updates their admin email (officer mail)
     * based on the request data, and saves the changes.
     *
     * @param  \Illuminate\Http\Request  $request  The HTTP request containing the new admin email (officer mail)
     * @param  int  $id  The ID of the user whose admin email is to be updated
     * @return \Illuminate\Http\JsonResponse  JSON response containing the updated user information
    */
    public function updateOfficer(Request $request, $id)
    {
        // Find the user by ID
        $user = User::findOrFail($id);
        
        // Update the user's admin email (officer mail) based on the request data
        $user->admin_mail = $request->officerMail;
        $user->save();

        return response()->json($user);
    }

    /**
     * Display user approval interface or message based on user status.
     *
     * Retrieves user data from the database with a specific status (active),
     * renders the user approval interface using Inertia.js if found, or displays
     * a message indicating the user is already approved if not found.
     *
     * @param  int  $userId  The ID of the user to approve
     * @return \Inertia\Response  Inertia response rendering either 'UserApproval' or 'UserApprovalMessage'
    */
    public function approval($userId)
    {
        try {
            // Retrieve the user data from the database with an active status
            $user = User::where('user_status_id', 1)->findOrFail($userId);

            // Render the user approval interface if user found
            return inertia::render('UserApproval', [
                'userData' => $user ? $user->toArray() : null,
            ]);

        } catch (ModelNotFoundException $e) {
            // Handle the case where the user is not found (already approved)
            return inertia::render('UserApprovalMessage', [
                'message' => 'User Already Approved.',
            ]);
        }
    }

    /**
     * Approve a user by updating their status and details.
     *
     * This method finds a user by ID, updates their information with data from
     * the request, and sets their status to 'active' (user_status_id = 2).
     * Returns a JSON response indicating success or failure.
     *
     * @param  \Illuminate\Http\Request  $request  The request instance containing user data
     * @param  int  $id  The ID of the user to approve
     * @return \Illuminate\Http\JsonResponse  JSON response indicating success or failure
    */
    public function approveUser(Request $request, $id)
    {
        try {
            // Find the user by ID
            $user = User::findOrFail($id);
        
            // Update all user data with the form data
            $user->update($request->all());
            // Set user_status_id to 2 for active status
            $user->update(['user_status_id' => 2]);

            // Return a JSON response indicating success
            return response()->json(['message' => 'User approved successfully'], 200);
        } catch (\Exception $e) {
            // Return a JSON response indicating failure
            return response()->json(['error' => 'Failed to approve user'], 500);
        }
    }

    /**
     * Reject a user by updating their status and details.
     *
     * This method finds a user by ID, updates their information with data from
     * the request, and sets their status to 'rejected' (user_status_id = 9).
     * Returns a JSON response indicating success or failure.
     *
     * @param  \Illuminate\Http\Request  $request  The request instance containing user data
     * @param  int  $id  The ID of the user to reject
     * @return \Illuminate\Http\JsonResponse  JSON response indicating success or failure
    */
    public function rejectUser(Request $request, $id)
    {
        try {
            // Find the user by ID
            $user = User::findOrFail($id);
        
            // Update all user data with the form data
            $user->update($request->all());
        
            // Set user_status_id to 9 for reject status
            $user->update(['user_status_id' => 9]);
        
            // Return a JSON response indicating success
            return response()->json(['message' => 'User rejected successfully'], 200);
        } catch (\Exception $e) {
            // Return a JSON response indicating failure
            return response()->json(['error' => 'Failed to reject user'], 500);
        }
    }

    /**
     * Fetch users related to the authenticated admin.
     *
     * This method retrieves the email of the authenticated admin and fetches all
     * users who have this email set as their admin_mail. Returns a JSON response
     * containing the list of users.
     *
     * @param  \Illuminate\Http\Request  $request  The request instance
     * @return \Illuminate\Http\JsonResponse  JSON response containing the list of users
    */
    public function getUsersByAdmin(Request $request)
    {
        // Retrieve the email of the authenticated admin
        $adminMail = $request->user()->email;
 
        // Fetch users related to the admin based on the admin_mail field
        $users = User::where('admin_mail', $adminMail)->get();

        // Return a JSON response containing the list of users
        return response()->json($users);
    }

    /**
     * Fetch user details by user ID including admin data.
     *
     * @param  int  $userId
     * @return \Illuminate\Http\JsonResponse
    */
    public function getUserById($userId)
    {
        $user = User::with('adminData') // Eager load the admin relationship
                    ->find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Fetch the admin's name based on the admin_mail field
        $admin = User::where('email', $user->admin_mail)->first();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email_verified_at' => $user->email_verified_at,
            'gone_at' => $user->gone_at,
            'admin_name' => $admin ? $admin->name : null, // Admin's name fetched from users table
        ]);
    }

    /**
     * Generate a report of users added and left within a specified month and year.
     *
     * This method retrieves the month and year from the request query parameters
     * and generates a report of users who were added or left within that period.
     * Returns a JSON response containing the lists of added and left users.
     *
     * @param  \Illuminate\Http\Request  $request  The request instance
     * @return \Illuminate\Http\JsonResponse  JSON response containing added and left users
    */
    public function getAddAndLeaveReport(Request $request)
    {
        // Retrieve month and year from query parameters
        $month = $request->query('month');
        $year = $request->query('year');

        // Validate that month and year are provided
        if (!$month || !$year) {
            return response()->json([
                'error' => 'Month and Year are required'
            ], 400);
        }

        // Create start and end dates for the specified month and year
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        // Fetch users who left within the specified period
        $leftUsers = User::whereIn('user_status_id', [3, 5, 11])
            ->whereBetween('gone_at', [$startDate, $endDate])
            ->get(['id', 'name']);

        // Fetch users who were added within the specified period
        $addedUsers = User::where('user_status_id', '!=', 1)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get(['id', 'name']);

        // Return a JSON response containing lists of added and left users
        return response()->json([
            'addedUsers' => $addedUsers,
            'leftUsers' => $leftUsers
        ]);
    }

    /**
     * Delete a user by updating their status and setting the gone_at column.
     *
     * This method validates the request data, finds the user by ID, updates the user's status,
     * sets the current timestamp in the gone_at column, and saves the changes.
     * Returns a JSON response indicating success or failure.
     *
     * @param \Illuminate\Http\Request $request  The request instance
     * @param int $id  The ID of the user to be deleted
     * @return \Illuminate\Http\JsonResponse  JSON response indicating success or error
    */
    public function deleteUser(Request $request, $id)
    {
        // Validate the request data to ensure 'status_id' is provided and is an integer
        $request->validate([
            'status_id' => 'required|integer',
        ]);

        // Find the user by ID
        $user = User::find($id);

        // Check if the user exists
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Update the user's status and set the gone_at column to the current timestamp
        $user->user_status_id = $request->input('status_id');
        $user->gone_at = Carbon::now();
        $user->save();

        // Return a JSON response indicating the user was deleted successfully
        return response()->json(['message' => 'User deleted successfully']);
    }

    /**
     * Retrieve the currently authenticated user's details.
     *
     * This method fetches the ID of the currently authenticated user, joins the users and admins tables
     * to get the user's details along with their admin status, and returns the data as a JSON response.
     * If the user is not authenticated, it returns a 401 error.
     *
     * @param \Illuminate\Http\Request $request  The request instance
     * @return \Illuminate\Http\JsonResponse  JSON response containing the user details or an error message
    */
    public function getCurrentUser(Request $request)
    {
        // Get the ID of the currently authenticated user
        $userId = Auth::id();

        // Check if the user is authenticated
        if ($userId) {
            // Fetch the user details along with admin status by joining the users and admins tables
            $user = DB::table('users')
                ->leftJoin('admins', 'users.id', '=', 'admins.user_id')
                ->select('users.*', 'admins.admin as is_admin')
                ->where('users.id', $userId)
                ->first();

            // Return the user details as a JSON response
            return response()->json($user);
        } else {
            // Return a 401 error if the user is not authenticated
            return response()->json(['error' => 'User not authenticated'], 401);
        }
    }
}
