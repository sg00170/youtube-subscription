<h2> Channel </h2>

<div>My Channels</div>
@if($channel)
    <div>
        <div>{{ $channel->title }}</div>
        <div>Thumbnails</div>
        <image src="{{ json_decode($channel->thumbnails)->default->url }}" alt="{{ $channel->title }}"/>
        <div>Description</div>
        <div style="margin-bottom:10px;">{{ $channel->discription }}</div>
    </div>
@else
    Create Channel
    <a href="{{ route('createIndex') }}"> + </a>
@endif