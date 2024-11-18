<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentDetailController extends Controller
{
    public function index(): View
    {
        return view('studentdetails', [

        ]);
    }
}
