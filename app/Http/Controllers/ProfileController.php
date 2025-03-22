<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        return view('profile', []);
    }

    /**
     * Update Project data
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $field = $request->input('field');
        $value = $request->input('value');

        $rules = [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'digits:10'],
        ];

        if (!array_key_exists($field, $rules)) {
            return response()->json(['success' => false, 'message' => 'Champ invalide.'], 400);
        }

        $request->validate([
            'field' => 'required|in:name,email,phone',
            'value' => $rules[$field],
        ]);

        $user = auth()->user();
        $user->{$field} = $value;
        $user->save();

        return response()->json(['success' => true]);
    }


}
