<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile', []);
    }

    public function update(Request $request)
    {
        // On attend deux champs : 'field' et 'value'
        $data = $request->validate([
            'field' => 'required|in:name,email',
            'value' => 'required|string|max:255'
        ]);

        $user = auth()->user();

        // Mise à jour dynamique
        $user->{$data['field']} = $data['value'];
        $user->save();

        return response()->json(['success' => true]);
    }

}
