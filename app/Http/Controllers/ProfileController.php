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
        $data = $request->validate([
            'field' => 'required|in:name,email',
            'value' => 'required|string|max:255'
        ]);

        $user = auth()->user();

        $user->{$data['field']} = $data['value'];
        $user->save();

        return response()->json(['success' => true]);
    }

}
