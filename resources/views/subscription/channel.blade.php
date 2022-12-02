<h2> Channels </h2>

<div> Channel List </div>
@forelse($channels as $index => $channel)
    <div style="border: 5px solid blue">
            
        <div>{{ $index + 1 }}. {{ $channel->title }}</div>
        <div>Thumbnails</div>
        <image src="{{ json_decode($channel->thumbnails)->default->url }}" alt="{{ $channel->title }}"/>
        <div>Description</div>
        <div style="margin-bottom:10px;">{{ $channel->discription }}</div>
        @if($channel->subscription)
            <div>구독 신청 완료</div>
        @else
            <form method='POST' action="{{ route('subscription.subscribe', ['id' => $channel->id]) }}">
                @csrf
                <button type='submit'> 구독 신청 </button>
            </form>
        @endif
    </div>
@empty
    <div> 구독 신청 리스트 X </div>
@endforelse