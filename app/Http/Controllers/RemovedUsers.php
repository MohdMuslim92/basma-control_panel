<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use \App\Models\User;

class RemovedUsers extends Controller
{
    public function index()
    {
        return Inertia::render('RemovedUsers');
    }

        /**
     * Fetch removed users data
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
    */
    public function getAllRemovedUsers()
    {
        try {
            $users = DB::table('users')
            ->whereIn('user_status_id', [3, 4, 5, 11])
            ->get();
            return response()->json($users);
        } catch (\Exception $e) {
            Log::error('Error fetching all removed users: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch all removed users'], 500);
        }
    }

    /**
     * Fetch removed users data with pagination.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
    */
    public function getRemovedUsers(Request $request)
    {
        try {
            $users = DB::table('users')
                ->whereIn('user_status_id', [3, 4, 5, 11])
                ->paginate(10); // Adjust the number per page as needed
                
            return response()->json($users);
        } catch (\Exception $e) {
            Log::error('Error fetching removed users: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch removed users'], 500);
        }
    }

    /**
     * Retrieve a user (set status to active).
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function retrieveUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->user_status_id = 2; // Set status to active
            $user->save();
            return response()->json(['success' => 'User retrieved successfully']);
        } catch (\Exception $e) {
            Log::error('Error retrieving user: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to retrieve user'], 500);
        }
    }
}
