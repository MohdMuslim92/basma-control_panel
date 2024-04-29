<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Office;

class OfficeController extends Controller
{
    public function index()
    {
        // Fetch the offices from the database
        $offices = Office::all();;

        return response()->json($offices);
    }

}
