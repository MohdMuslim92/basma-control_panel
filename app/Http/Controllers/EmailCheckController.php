<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class EmailCheckController extends Controller
{
    public function checkEmail(Request $request)
    {
        // Validate the email field
        $request->validate([
            'email' => 'required|email',
        ]);

        // Check if the email already exists in the users table
        $exists = User::where('email', $request->email)->exists();

        // Return the result as a JSON response
        return response()->json(['exists' => $exists]);
    }
}
