<?php

namespace App\Http\Controllers;

use App\Models\Unit;

class UnitController extends Controller
{
    public function index()
    {
        return [
            'units' => Unit::with('lessons')->get()
        ];
    }
}
