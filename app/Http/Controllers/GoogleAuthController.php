<?php

namespace App\Http\Controllers;

use App\Http\Resources\googleUserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
     public function loginGoogleUser(Request $request)
    {
        $validated = $request->validate([
            'id_token' => 'required|string',
        ]);


//        $tokenG = "eyJhbGciOiJSUzI1NiIsImtpZCI6IjA3YjgwYTM2NTQyODUyNWY4YmY3Y2QwODQ2ZDc0YThlZTRlZjM2MjUiLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJodHRwczovL2FjY291bnRzLmdvb2dsZS5jb20iLCJhenAiOiI3NzM0MTIzMDM0MjEtbWN2ZzZkZHV0c3E0ZTltcnQ2a3UycGx2aWhuYjdtOXEuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJhdWQiOiI3NzM0MTIzMDM0MjEtbWN2ZzZkZHV0c3E0ZTltcnQ2a3UycGx2aWhuYjdtOXEuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJzdWIiOiIxMTExNTEyOTc2OTI1NTg2MDMwMDIiLCJlbWFpbCI6ImZhYmllbjQyNDBmbGF2aW9AZ21haWwuY29tIiwiZW1haWxfdmVyaWZpZWQiOnRydWUsIm5iZiI6MTc0NjM4OTEzNCwibmFtZSI6Ik1hbWluaWFpbmEgRmFiaWVuIEZsYXZpbyBBbmRyaWFuYW5kcmFzYW5hIiwicGljdHVyZSI6Imh0dHBzOi8vbGgzLmdvb2dsZXVzZXJjb250ZW50LmNvbS9hL0FDZzhvY0tLUUJsMjFuWjAtaGpWMGlWN0cxQ3pvMmV1cUFMcTh0S1c3LXRZMkxnbDkxaTZaOXc9czk2LWMiLCJnaXZlbl9uYW1lIjoiTWFtaW5pYWluYSBGYWJpZW4gRmxhdmlvIiwiZmFtaWx5X25hbWUiOiJBbmRyaWFuYW5kcmFzYW5hIiwiaWF0IjoxNzQ2Mzg5NDM0LCJleHAiOjE3NDYzOTMwMzQsImp0aSI6IjIxNDQ0ZmNiNmNiMDdkNTczNGUwZmJiZmMyNDMzZGY1MDUyYTA2ZjgifQ.uH8944sulN0Fgy1c-Bt1C2fRT5XAoGPLJeSwY4ZW_aLdBlQQMkgXaOHfXgdQ63Ib7na5-uSYyVnvpUJPZWEgAr3PG7868DS5GLJ7IBdqNMeI8ZnLB6RGT_nrcapUGu9RB96n0m0xfDzdOx-mAi01P6fPkCzorvldkIklen_q2IgmLK9YZy1WDOrwYK9gTrKMLOu24s3acW0N3Aakyvv2IWLcm7dz8jLXpHiQnWzyB6Lccod3uARmnwSZRCLr6Pg_RRYRnUJcknlUp4ZLVBMLIgAWzbPjteMa_N3LjPAhkQCpZ_oiQma7nGSNJLhm5vHOUe1osD54Nl4H3nF-kHib0w";

        if(request()->has("id_token"))
        {
            $idGoogleToken = request()->get("id_token");

            $parts = explode(".", $idGoogleToken);

            if(count($parts) !== 3)
            {
                return response()->json([
                    "message" => "Invalid ID token structure"
                ], 403);
            }

            $payload = $parts[1];

            $payload = strtr($payload, "-_", "+/");

            $payload = base64_decode($payload);

            if(!$payload)
            {
                return response()->json([
                    "message" => "Unable to decode token payload"
                ], 403);
            }

            $data = json_decode($payload);

            $existingUser = User::where("email", $data->email)->first();

            if ($existingUser) {

                $token = $existingUser->createToken('google-login-token')->plainTextToken;

                return response()->json([
                    "message" => "Logged in successfully.",
                    "user" => new GoogleUserResource($existingUser),
                    "token" => $token
                ], 200);
            }


            $user = User::create([
                'name' => $data->name,
                'email' => $data->email,
                'google_id' => $data->sub,
                'email_verified_at' => now(),
                'password' => Hash::make(Str::random(24))
            ]);

            $token = $user->createToken($user->name)->plainTextToken;

            return response()->json([
                "message" => "Logged In Successfully.",
                "user" => new googleUserResource($user),
                "token" => $token
            ], 201);

        } else{
            return response()->json([
                "message" => "Cannot Login Register with Google"
            ]);
        }

    }

    public function logoutGoogleUser(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'User logged out',
        ], 200);
    }
    public function me(Request $request)
    {
        return response()->json([
            "user" => Auth::user()
        ], 200);
    }
}

