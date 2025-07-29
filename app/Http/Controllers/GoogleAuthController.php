<?php

namespace App\Http\Controllers;

use App\Http\Resources\googleUserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class GoogleAuthController extends Controller
{
     public function loginGoogleUser(Request $request)
    {
        $validated = $request->validate([
            'id_token' => 'required|string',
        ]);


        $response = Http::get("https://oauth2.googleapis.com/tokeninfo", [
            'id_token' => $validated['id_token'],
        ]);

        if (!$response->ok()) {
            return response()->json(['error' => 'Token Google invalide'], 401);
        }

        $googleUser = $response->json();


        $user = User::firstOrCreate(
            ['email' => $googleUser['email']],
            [
                'name' => $googleUser['name'] ?? $googleUser['email'],
                'email_verified_at' => now(),
                'password' => bcrypt(str()->random(32)),
            ]
        );

        Auth::login($user);

        return response()->json(['message' => 'Connecté avec succès']);
    }

    public function logoutGoogleUser(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['message' => 'Déconnecté avec succès']);
    }
}
