<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Province;

class ProvinceController extends Controller
{
    public function show($state)
    {
        $provinces = Province::where('state_id', $state)->get(); // Fetch provinces by state ID
        return response()->json($provinces); // Return provinces as JSON
    }
}
