<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\State;

class StateController extends Controller
{
    public function index()
    {
        $states = State::all(); // Fetch all states
        return response()->json($states); // Return states as JSON
    }
}
