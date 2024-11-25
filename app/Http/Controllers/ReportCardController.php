<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportCardController extends Controller
{
    public function index(int $studentId): View
    {
        return view('reports', ['selectedStudentId' => $studentId]);
    }
}
