<h2> Channel Create </h2>

<form method='POST' action="{{ route('create') }}">
    @csrf
    <div> Channel ID </div>
    <input type='text' name="channel_id" placeholder="추가하실 채널 id를 입력해주세요."/>

    <button type='submit'> 추가 </button>
</form>