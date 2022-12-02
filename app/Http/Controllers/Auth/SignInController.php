<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\user;

use Hash;
use Auth;

use GuzzleHttp\Client;

class SignInController extends Controller
{
    /**
     * 로그인 페이지
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function loginIndex(Request $request)
    {
        return view('auth.signin');
    }

    /**
     * 로그인
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function login(Request $request)
    {
        try{
            $request->validate([
                'email' => 'bail|required|string|email|exists:users,email',
                // 'password' => 'bail|required|string'
            ]);
    
            $user = User::where('email', $request->email)->first();
            // if(Hash::check($request->password, $user->password)){
            //     Auth::guard('web')->login($user);
            // }
            Auth::guard('web')->login($user);

            return redirect('/');
        }
        catch(\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * 로그아웃
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function logout(Request $request)
    {
        try{
            // $user = $request->user();
            // $client = new Client;

            // if(now()->greaterThan($user->access_token_expired_at)){
            //     // 재인증
            //     $response = $client->post("https://accounts.google.com/o/oauth2/token?client_id=893980397774-k62bs9uu5b9jpu8uim77g9pt113ep68p.apps.googleusercontent.com&redirect_uri=http://localhost:8000/social/google&refresh_token={$user->refresh_token}&grant_type=refresh_token&client_secret=LZMoRTuftAr-0TR5Hz-IetKU");
            //     $result = (object)[
            //         'status' => $response->getStatusCode(),
            //         'headers' => $response->getHeaders(),
            //         'body' => $response->getBody(),
            //         'contents' => json_decode($response->getBody()->getContents())
            //     ];

            //     $user->update([
            //         'access_token' => $result->contents->access_token,
            //         'access_token_expired_at' => now()->addSeconds($result->contents->expires_in),
            //     ]);
            // }

            // $response = $client->get("https://accounts.google.com/o/oauth2/revoke?token={$user->access_token}");
            // $result = (object)[
            //     'status' => $response->getStatusCode(),
            //     'headers' => $response->getHeaders(),
            //     'body' => $response->getBody(),
            //     'contents' => json_decode($response->getBody()->getContents())
            // ];

            Auth::logout();

            return redirect('/');
        }
        catch(\Exception $exception) {
            throw $exception;
        }
    }
}
