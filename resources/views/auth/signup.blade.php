<h2> SignUp </h2>

<form method="POST" route="{{ route('signup') }}">
    @csrf
    <input type='hidden' name='code' value='{{ $code }}'/>
    <div style="margin-bottom:10px"> 
        <div>Email</div>
        <input type='email' name='email'/>
    </div>

    <div style="margin-bottom:10px"> 
        <div>Password</div>
        <input type='password' name='password'/>
    </div>

    <button type='submit'>SignUp</button>
</form>