<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Ces identifiants ne correspondent pas à nos enregistrements.',
            ])->onlyInput('email');
        }

        if (!$user->isActive()) {
            return back()->withErrors([
                'email' => 'Votre compte est inactif. Contactez l\'administrateur.',
            ])->onlyInput('email');
        }

        if ($user->tenant && !$user->tenant->isActive()) {
            return back()->withErrors([
                'email' => 'Le compte de votre entreprise est suspendu.',
            ])->onlyInput('email');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user->updateLastLogin();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Ces identifiants ne correspondent pas à nos enregistrements.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'terms' => ['required', 'accepted'],
        ], [
            'company_name.required' => 'Le nom de l\'entreprise est requis.',
            'name.required' => 'Votre nom est requis.',
            'email.required' => 'L\'adresse email est requise.',
            'email.email' => 'L\'adresse email n\'est pas valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'password.required' => 'Le mot de passe est requis.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'terms.accepted' => 'Vous devez accepter les conditions d\'utilisation.',
        ]);

        $tenant = Tenant::create([
            'name' => $request->company_name,
            'slug' => Str::slug($request->company_name) . '-' . Str::random(5),
            'email' => $request->email,
            'currency' => 'EUR',
            'timezone' => 'Europe/Paris',
            'locale' => 'fr',
            'status' => 'active',
            'plan' => 'free',
            'trial_ends_at' => now()->addDays(14),
        ]);

        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'active',
        ]);

        $user->assignRole('admin');

        Auth::login($user);

        return redirect()->route('dashboard')->with('status', 'Bienvenue ! Votre compte a été créé avec succès.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
