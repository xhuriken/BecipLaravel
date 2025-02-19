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
            'users' => User::all(),
        ]);
    }

    public function updateCompany(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'name'       => 'required|string|max:255',
        ]);

        $company = Company::findOrFail($request->company_id);
        $company->name = $request->name;
        $company->save();

        return response()->json(['success' => true]);
    }


    public function deleteCompany(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
        ]);

        $company = Company::findOrFail($request->company_id);
        $company->delete();

        return response()->json(['success' => true]);
    }

    public function updateUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$request->user_id,
            'role' => 'required|in:engineer,drawer,secretary,client',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->company_id = $request->company_id ?: null;
        $user->save();

        return response()->json(['success' => true]);
    }

    public function deleteUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->delete();

        return response()->json(['success' => true]);
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

    public function addcompany(Request $request){
        if ($request->isMethod('post')) {
            $request->validate(['name' => 'required|string|max:255',]);

            Company::create([
                'name' => $request['name'],
            ]);
            return redirect()->back()->with('success', 'Entreprise ajouté avec succès.');
        }
        return view('usermanager.addcompany');
    }

}
