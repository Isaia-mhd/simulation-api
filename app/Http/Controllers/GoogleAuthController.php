<?php

namespace App\Http\Controllers;

use App\Http\Resources\googleUserResource;
use Illuminate\Http\Request;

class GoogleAuthController extends Controller
{
     public function loginGoogleUser(Request $request)
    {
        $tokenG = "eyJhbGciOiJSUzI1NiIsImtpZCI6IjA3YjgwYTM2NTQyODUyNWY4YmY3Y2QwODQ2ZDc0YThlZTRlZjM2MjUiLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJodHRwczovL2FjY291bnRzLmdvb2dsZS5jb20iLCJhenAiOiI3NzM0MTIzMDM0MjEtbWN2ZzZkZHV0c3E0ZTltcnQ2a3UycGx2aWhuYjdtOXEuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJhdWQiOiI3NzM0MTIzMDM0MjEtbWN2ZzZkZHV0c3E0ZTltcnQ2a3UycGx2aWhuYjdtOXEuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJzdWIiOiIxMTExNTEyOTc2OTI1NTg2MDMwMDIiLCJlbWFpbCI6ImZhYmllbjQyNDBmbGF2aW9AZ21haWwuY29tIiwiZW1haWxfdmVyaWZpZWQiOnRydWUsIm5iZiI6MTc0NjM4OTEzNCwibmFtZSI6Ik1hbWluaWFpbmEgRmFiaWVuIEZsYXZpbyBBbmRyaWFuYW5kcmFzYW5hIiwicGljdHVyZSI6Imh0dHBzOi8vbGgzLmdvb2dsZXVzZXJjb250ZW50LmNvbS9hL0FDZzhvY0tLUUJsMjFuWjAtaGpWMGlWN0cxQ3pvMmV1cUFMcTh0S1c3LXRZMkxnbDkxaTZaOXc9czk2LWMiLCJnaXZlbl9uYW1lIjoiTWFtaW5pYWluYSBGYWJpZW4gRmxhdmlvIiwiZmFtaWx5X25hbWUiOiJBbmRyaWFuYW5kcmFzYW5hIiwiaWF0IjoxNzQ2Mzg5NDM0LCJleHAiOjE3NDYzOTMwMzQsImp0aSI6IjIxNDQ0ZmNiNmNiMDdkNTczNGUwZmJiZmMyNDMzZGY1MDUyYTA2ZjgifQ.uH8944sulN0Fgy1c-Bt1C2fRT5XAoGPLJeSwY4ZW_aLdBlQQMkgXaOHfXgdQ63Ib7na5-uSYyVnvpUJPZWEgAr3PG7868DS5GLJ7IBdqNMeI8ZnLB6RGT_nrcapUGu9RB96n0m0xfDzdOx-mAi01P6fPkCzorvldkIklen_q2IgmLK9YZy1WDOrwYK9gTrKMLOu24s3acW0N3Aakyvv2IWLcm7dz8jLXpHiQnWzyB6Lccod3uARmnwSZRCLr6Pg_RRYRnUJcknlUp4ZLVBMLIgAWzbPjteMa_N3LjPAhkQCpZ_oiQma7nGSNJLhm5vHOUe1osD54Nl4H3nF-kHib0w";

        if(request()->has("googleToken"))
        {
            $idGoogleToken = request()->get("googleToken");

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


            // check if the user info already in database
            $existUser = User::where("email", $data->email)->exists();
            if($existUser)
            {
                $user = User::where("email", $data->email)->first();
                $token = $user->createToken($user->name)->plainTextToken;

                return response()->json([
                    "message" => "Logged In Successfully.",
                    "user" => new googleUserResource($user),
                    "token" => $token
                ], 200);
            }

            // register the user if not in database yet
            $user = User::create([
                'name' => $data->name,
                'email' => $data->email,
                'google_id' => $data->sub,
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
}
