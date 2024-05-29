<?php

namespace App\Http\Controllers;
use Inertia\Inertia;

use Illuminate\Http\Request;

class martin extends Controller
{
    public function hej(){
        return Inertia::render('martin');
    }
}
