<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        return view('usermanager', [
            'newPasswordGenerated' => $this->genNewPassword(),
            'companies' => Company::all(),
        ]);
    }

    function genNewPassword($lenght = 12) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
        $password = '';
        $maxIndex = strlen($chars) - 1;
        for ($i = 0; $i < $lenght; $i++) {
            $password .= $chars[random_int(0, $maxIndex)];
        }
        return $password;
    }

    public function adduser(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'role' => 'required|in:engineer,drawer,secretaty,client',
                'company_id' => 'nullable|exists:companies,id'
            ]);

            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'company_id' => $request->company_id,
            ]);

            return redirect()->back()->with('success', 'Utilisateur ajouté avec succès.');
        }

        $companies = Company::all();
        return view('usermanager.adduser', compact('companies'));
    }

}
