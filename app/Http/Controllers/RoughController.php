<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

class RoughController extends Controller
{
    public function index()
    {
        // Logic for the index method
        return view('rough');
    }
}