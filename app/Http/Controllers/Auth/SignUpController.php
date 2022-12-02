<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

use Hash;
use Auth;
use Log;

use GuzzleHttp\Client;

class SignUpController extends Controller
{   
    /**
     * 회원가입
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function signup(Request $request)
    {
        try{
            $request->validate([
                'code' => 'bail|required|string',
            ]);

            Log::info("[Signup] =========================");

            $client = new Client;

            $responseToken = $client->post("https://accounts.google.com/o/oauth2/token?code={$request->code}&client_id=893980397774-k62bs9uu5b9jpu8uim77g9pt113ep68p.apps.googleusercontent.com&client_secret=LZMoRTuftAr-0TR5Hz-IetKU&redirect_uri=http://localhost:8000/social/google&grant_type=authorization_code");
            $resultToken = (object)[
                'status' => $responseToken->getStatusCode(),
                'headers' => $responseToken->getHeaders(),
                'body' => $responseToken->getBody(),
                'contents' => json_decode($responseToken->getBody()->getContents())
            ];

            // $responseProfie = $client->get("https://www.googleapis.com/auth/userinfo.profile?access_token={$resultToken->contents->access_token}");
            $responseProfie = $client->get("https://www.googleapis.com/oauth2/v3/tokeninfo?id_token={$resultToken->contents->id_token}");
            $resultProfile = (object)[
                'status' => $responseProfie->getStatusCode(),
                'headers' => $responseProfie->getHeaders(),
                'body' => $responseProfie->getBody(),
                'contents' => json_decode($responseProfie->getBody()->getContents())
            ];

            $user = User::where('email', $resultProfile->contents->email)->first();
            if(!$user){
                // $password = \Illuminate\Support\Str::random(20);
                $password = 'secret';
                $user = User::create([
                    // 'email' => \Illuminate\Support\Str::random(10).'@email.com',
                    'email' => $resultProfile->contents->email,
                    'password' => Hash::make($password),
                    'access_token' => $resultToken->contents->access_token,
                    'access_token_expired_at' => now()->addSeconds($resultToken->contents->expires_in),
                    'refresh_token' => $resultToken->contents->refresh_token,
                    'scopes' => $resultToken->contents->scope
                ]);

                Log::info("[Signup] ID : {$user->email} | PW : {$password}");
                Log::info("[Signup] =========================\n");
            }
            
            // event(new \Illuminate\Auth\Events\Registered($user));
            Auth::guard('web')->login($user);

            return redirect('/');
        }
        catch(\Exception $exception){
            throw $exception;
        }
    }
}
