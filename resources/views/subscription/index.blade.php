<h2> Subscriptions </h2>

<div> My Subscriptions </div>
@forelse($subscriptions as $index => $subscription)
    <div style="border: 1px solide yellow">
        <div>{{ $index + 1 }}. {{ $subscription->channel->title }}</div>
        <div>Thumbnails</div>
        <image src="{{ json_decode($subscription->channel->thumbnails)->default->url }}" alt="{{ $subscription->channel->title }}"/>
        <div>Description</div>
        <div style="margin-bottom:10px;">{{ $subscription->channel->discription }}</div>
    </div>
@empty
    <div> No Subscription List </div>
@endforelse