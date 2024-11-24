<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Exception;

class GoogleController extends Controller
{
    // Redirige al usuario a la página de autenticación de Google
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // Maneja la respuesta de Google y autentica al usuario
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Verificar si el usuario ya existe en la base de datos
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Si el usuario ya existe, inicia sesión
                Auth::login($user);
            } else {
                // Si no existe, crea un nuevo usuario y luego inicia sesión
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => bcrypt('password'), // Contraseña dummy
                ]);

                Auth::login($user);
            }

            // Redirige a la página de bienvenida
            return redirect()->intended('/welcome');

        } catch (Exception $e) {
            return redirect('/welcome')->withErrors(['msg' => 'logeado' . $e->getMessage()]);
        }
    }
}
