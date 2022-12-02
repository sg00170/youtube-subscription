<h2> User </h2>

<div>Email</div>
<input type='email' name='email' value="{{ $user->email }}"/>

<div>
    <a href="{{ route('channel') }}" > My Channels </a>
</div>
<div>
    <a href="{{ route('subscription') }}" > My Subscriptions </a>
</div>