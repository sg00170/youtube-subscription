<?php

namespace App\Http\Controllers\Channel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Channel;

use DB;

use GuzzleHttp\Client;

class ChannelController extends Controller
{
    /**
     * 내 채널 페이지.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function __invoke(Request $request)
    {
        return view('channel.index', [
            'channel' => $request->user()->channel
        ]);
    }

    /**
     * 내 채널 추가 페이지.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function createIndex(Request $request)
    {
        return view('channel.create');
    }

    /**
     * 내 채널 추가.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function create(Request $request)
    {
        try{
            $user = $request->user();
            $request->merge(['user_id' => $user->id]);
            $request->validate([
                'user_id' => 'bail|required|numeric|unique:channels,user_id',
                'channel_id' => 'bail|required|string|unique:channels,channel_id'
            ]);

            $client = new Client;

            if(now()->greaterThan($user->access_token_expired_at)){
                // 재인증
                $response = $client->post("https://accounts.google.com/o/oauth2/token?client_id=893980397774-k62bs9uu5b9jpu8uim77g9pt113ep68p.apps.googleusercontent.com&redirect_uri=http://localhost:8000/social/google&refresh_token={$user->refresh_token}&grant_type=refresh_token&client_secret=LZMoRTuftAr-0TR5Hz-IetKU");
                $result = (object)[
                    'status' => $response->getStatusCode(),
                    'headers' => $response->getHeaders(),
                    'body' => $response->getBody(),
                    'contents' => json_decode($response->getBody()->getContents())
                ];

                $user->update([
                    'access_token' => $result->contents->access_token,
                    'access_token_expired_at' => now()->addSeconds($result->contents->expires_in),
                ]);
            }

            $headers = [
                'Authorization' => "Bearer {$user->access_token}",
            ];

            $response = $client->get("https://www.googleapis.com/youtube/v3/channels?part=snippet&id={$request->channel_id}", [
                'headers' => $headers
            ]);
            $result = (object)[
                'status' => $response->getStatusCode(),
                'headers' => $response->getHeaders(),
                'body' => $response->getBody(),
                'contents' => json_decode($response->getBody()->getContents())
            ];

            if($request->channel_id === $result->contents->items[0]->id){
                Channel::create([
                    'user_id' => $user->id,
                    'channel_id' => $result->contents->items[0]->id,
                    'title' => $result->contents->items[0]->snippet->title,
                    'description' => $result->contents->items[0]->snippet->description,
                    'thumbnails' => json_encode($result->contents->items[0]->snippet->thumbnails)
                ]);
            }
            else{
                throw new \Exception('채널 정보가 다릅니다.');
            }

            return redirect()->route('channel');
        }
        catch(\Exception $exception){
            DB::rollback();
            throw $exception;
        }
    }
}
