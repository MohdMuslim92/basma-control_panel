<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Money;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserSharesAndPaymentsController extends Controller
{
    public function index()
    {
        return Inertia::render('UserSharesAndPayments');
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

    public function getPaidUsers(Request $request)
    {
        // Validate the request data
        $request->validate([
            'month' => 'required|integer|between:1,12', // Ensure the month is between 1 and 12
            'year' => 'required|integer|between:2017,2037', // Ensure the year is between 2017 and 2037
        ]);
    
        // Extract month and year from the request
        $month = $request->month;
        $year = $request->year;
    
        // Calculate the start and end dates based on the provided month and year
        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate)); // Get the last day of the month
    
        // Fetch paid users based on the criteria
        $paidUsers = User::with('admin') // Eager load the admin relationship
            ->where('last_pay', '>=', $startDate)
            ->get()
            ->map(function ($user) {
                // Fetch the last payment type from the money table for each user
                $lastPayment = DB::table('money')
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->first();
    
                // Add the last payment type to the user object
                $user->last_payment_type = $lastPayment ? $lastPayment->type : null;
    
                return $user;
            });
    
        // Fetch unpaid users for the same month and year
        $unpaidUsers = User::where(function ($query) use ($startDate) {
            $query->whereNull('users.last_pay')
                ->orWhere('users.last_pay', '<', $startDate);
        })
        ->whereNotNull('users.admin_mail') // Ensure admin_mail is not null
        ->leftJoin('users as admins', 'users.admin_mail', '=', 'admins.email')
        ->select('users.*', 'admins.name as admin_name', 'admins.email as admin_email')
        ->get();
    
        // Return both paid and unpaid users
        return response()->json([
            'paidUsers' => $paidUsers,
            'unpaidUsers' => $unpaidUsers,
        ]);
    }

    public function getSharesReport(Request $request)
    {
        // Validate the request data
        $request->validate([
            'month' => 'required|integer|between:1,12', // Ensure the month is between 1 and 12
            'year' => 'required|integer|between:2017,2037', // Ensure the year is between 2017 and 2037
        ]);

        $month = $request->query('month');
        $year = $request->query('year');

        // Calculate the start and end dates for the query
        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        // Query the money table
        $payments = DB::table('money')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->select('user_id', 'amount', 'type')
            ->get();

        // Compute sums and user count
        $cash = $payments->where('type', 'Cash')->sum('amount');
        $bankTransfer = $payments->where('type', 'Bank Transfer')->sum('amount');
        $mobileBalance = $payments->where('type', 'Mobile Balance')->sum('amount');
        $total = $cash + $bankTransfer + $mobileBalance;
        $userCount = $payments->unique('user_id')->count();

        return response()->json([
            'userCount' => $userCount,
            'cash' => $cash,
            'bankTransfer' => $bankTransfer,
            'mobileBalance' => $mobileBalance,
            'total' => $total,
        ]);
    }

}