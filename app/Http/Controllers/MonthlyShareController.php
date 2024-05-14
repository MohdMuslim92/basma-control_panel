<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Money;
use App\Models\User;

class MonthlyShareController extends Controller
{
    public function index()
    {
        return Inertia::render('MonthlyShare');
    }

    public function payUser(Request $request)
    {
        // Validate the request data
        $request->validate([
            'months' => 'required|integer|max:30', // Ensure the number of months is provided and is a positive integer
            'paymentType' => 'required|string|max:14', // Ensure the payment type is provided and is a string
        ]);

        // Find the user by ID
        $user = User::findOrFail($request->userId);

        // Calculate the new last pay date
        $lastPay = $user->last_pay ? Carbon::parse($user->last_pay) : now();
        $newLastPay = $lastPay->copy()->addMonthsNoOverflow($request->months)->endOfMonth();

        // Update the user's last pay date
        $user->update(['last_pay' => $newLastPay]);

        // Insert a record into the Money table
        Money::create([
            'user_id' => $user->id,
            'amount' => $request->months * $user->monthlyShare,
            'type' => $request->paymentType,
            'admin_id' => auth()->id(), // The admin ID is stored in the authentication session
        ]);

        // Return a success response
        return response()->json(['message' => 'Payment processed successfully'], 200);
    }    
}
