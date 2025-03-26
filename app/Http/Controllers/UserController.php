<?php

namespace App\Http\Controllers;

use App\Mail\UpdatedEmailNotification;
use App\Models\Company;
use App\Models\User;
use Grosv\LaravelPasswordlessLogin\LoginUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Mail\NewUser;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
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

    /**
     * Update name of a company (with his id)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Delete company (with id)
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteCompany(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
        ]);

        $company = Company::findOrFail($request->company_id);
        $company->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Update User fields with id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $request->user_id,
            'role' => 'required|in:engineer,drawer,secretary,client',
            'company_id' => 'nullable|exists:companies,id',
            'phone' => 'nullable|digits:10',
        ]);

        $user = User::findOrFail($request->user_id);

        $oldEmail = $user->email;
        $user->phone = $request->phone ?? null;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;
        $user->company_id = $request->company_id ?: null;
        $user->save();

        // Si l'email a été modifié, on renvoie un mail
        if ($oldEmail !== $user->email) {
            $generator = new LoginUrl($user);
            $generator->setRedirectUrl('/');
            $urlHome = $generator->generate();

            $token = Password::createToken($user);
            $urlPassword = url(route('password.reset', [
                'token' => $token,
                'email' => $user->email,
            ]));

            Mail::to($user->email)->send(new UpdatedEmailNotification(
                $user->name,
                $urlHome,
                $urlPassword
            ));
        }

        return response()->json(['success' => true]);
    }
    /**
     * Delete with id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Generate password randomly
     * @param $lenght
     * @return string
     * @throws \Random\RandomException
     */
    function genNewPassword($lenght = 12) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
        $password = '';
        $maxIndex = strlen($chars) - 1;
        for ($i = 0; $i < $lenght; $i++) {
            $password .= $chars[random_int(0, $maxIndex)];
        }
        return $password;
    }

    /**
     * Add new user
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|object
     */
    public function adduser(Request $request)
    {
        if ($request->isMethod('post')) {
            // Vérification si c'est une requête AJAX
                $validatedData = $request->validate([
                    'name' => 'required|string|max:255',
                    'phone' => 'nullable|string|max:255',
                    'email' => 'required|email|unique:users,email',
                    'password' => 'required|string|min:8',
                    'role' => 'required|in:engineer,drawer,secretary,client',
                    'company_id' => 'nullable|exists:companies,id'
                ]);

                // Création de l'utilisateur
                $user = User::create([
                    'name' => $validatedData['name'],
                    'phone' => $validatedData['phone'],
                    'email' => $validatedData['email'],
                    'password' => Hash::make($validatedData['password']),
                    'role' => $validatedData['role'],
                    'company_id' => $validatedData['company_id'],
                ]);

                // Magic link Connexion
                $generator = new LoginUrl($user);
                $generator->setRedirectUrl('/');
                $url = $generator->generate();

                // Link Change Password
                $token = Password::createToken($user);

                $urlPassword = url(route('password.reset', [
                    'token' => $token,
                    'email' => $user->email,
                ]));

                // Envoi de l'email
                Mail::to($user->email)->send(new NewUser(
                    $user->name,
                    $url,
                    $validatedData['password'],
                    $urlPassword
                ));

                // Réponse JSON pour l'AJAX
                return response()->json([
                    'success' => true,
                    'message' => 'Utilisateur ajouté avec succès.',
                    'user_id' => $user->id,
                    'edit_url' => route('usermanager.updateuser'),
                    'delete_url' => route('usermanager.deleteuser'),
                ]);

        }

        $companies = Company::all();
        return view('usermanager.adduser', compact('companies'));
    }

    /**
     * Add new Company
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Http\RedirectResponse|object
     */
    public function addcompany(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $company = Company::create([
                'name' => $request['name'],
            ]);

            // Vérifie si c’est AJAX (fetch)
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'company' => $company,
                    'update_route' => route('usermanager.updatecompany'),
                    'delete_route' => route('usermanager.deletecompany')
                ]);
            }

            // Sinon, fallback vers la redirection
            return redirect()->back()->with('success', 'Entreprise ajoutée avec succès.');
        }

        return view('usermanager.addcompany');
    }
}
