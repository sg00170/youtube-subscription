<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Channel;
use App\Models\Subscription;

use GuzzleHttp\Client;

class SubscriptionController extends Controller
{
    /**
     * 내 구독 신청 목록
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function __invoke(Request $request)
    {
        return view('subscription.index', [
            'subscriptions' => $request->user()->subscriptions
        ]);
    }

    /**
     * 구독 신청 페이지.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function getChannelList(Request $request)
    {
        $user = $request->user();

        $mySubscriptionChannles = Subscription::with(['channel'])->where('user_id', $user->id)->get()->pluck('channel.id')->toArray();
        $channels = Channel::where('user_id', '!=', $user->id)->get()->map(function($channel) use($mySubscriptionChannles){
            $channel['subscription'] = false;
            if(array_search($channel->id, $mySubscriptionChannles) !== false){
                $channel['subscription'] = true;
            }

            return $channel;
        });
        
        return view('subscription.channel', compact('channels'));
    }

    /**
     * 구독 신청.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return array
     */
    public function subscribe(Request $request, $id)
    {
        try{
            $user = $request->user();
            $request->merge(['channel_id' => $id]);
            $request->validate([
                'channel_id' => 'bail|required|numeric|exists:channels,id'
            ]);

            $subscription = Subscription::where('user_id', $request->user()->id)
                                            ->where('channel_id', $request->channel_id)
                                            ->first();

            $client = new Client;

            if(!$subscription){
                // 채널
                $channel = Channel::find($request->channel_id);

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

                // 본인의 구독 내역에 해당 채널의 id가 있는 지 확인 (이미 구독하고있던 경우는..?)
                $headers = [
                    'Authorization' => "Bearer {$user->access_token}",
                ];
                
                $totalResults = 1;
                while($totalResults > 0){
                    $response = $client->get("https://www.googleapis.com/youtube/v3/subscriptions?mine=true&part=snippet&maxResults=50", [
                        'headers' => $headers
                    ]);
                    $result = (object)[
                        'status' => $response->getStatusCode(),
                        'headers' => $response->getHeaders(),
                        'body' => $response->getBody(),
                        'contents' => json_decode($response->getBody()->getContents())
                    ];

                    $flag = false;
                    if(isset($result->contents->data) && count($result->contents->data->items) > 0) {
                        foreach($result->contents->data->items as $subscription){
                            if($subscription->resourceId->channelId === $channel->id){
                                $flag = true;
                                $subscriptionInfo = $subscription;
                                break;
                            }
                        }
    
                        if($flag) break;
    
                        $totalResults = $result->contents->data->pageInfo->totalResults;
                    }
                    else{
                        $totalResults = 0;
                    }
                }

                if(!$flag && empty($subscriptionInfo)){
                    $response = $client->post("https://www.googleapis.com/youtube/v3/subscriptions?part=snippet", [
                        'headers' => $headers,
                        'json' => [
                            'snippet' => [
                                'resourceId' => [
                                    'kind' => "youtube#channel",
                                    'channelId' => $channel->channel_id
                                ]
                            ]
                        ]
                    ]);
                    $result = (object)[
                        'status' => $response->getStatusCode(),
                        'headers' => $response->getHeaders(),
                        'body' => $response->getBody(),
                        'contents' => json_decode($response->getBody()->getContents())
                    ];

                    Subscription::create([
                        'user_id' => $user->id,
                        'channel_id' => $channel->id,
                        'subscription_id' => $result->contents->id,
                        'subscribed_at' => \Carbon\Carbon::parse($result->contents->snippet->publishedAt)
                    ]);
                }

            }
            else{
                new \Exception('이미 구독신청한 채널입니다.');
            }

            return redirect('my/subscription');
        }
        catch(\Exception $exception){
            throw $exception;
        }
    }
}
