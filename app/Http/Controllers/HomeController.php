<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home', [
            'projects' => auth()->user()->projects(),
            'companies' => Company::all(),
            'engineers' => User::all()->where('role', 'engineer'),
            'clients' => User::where('role', 'client')->get(),
        ]);
    }
}
