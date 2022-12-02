<h2> SignIn </h2>

<form method="POST" route="{{ route('signin') }}">
    @csrf
    <div style="margin-bottom:10px"> 
        <div>Email</div>
        <input type='email' name='email'/>
    </div>

    <!-- <div style="margin-bottom:10px"> 
        <div>Password</div>
        <input type='password' name='password'/>
    </div> -->

    <button type='submit'>SignIn</button>
</form>

<a href="https://accounts.google.com/o/oauth2/auth?client_id={{ config('services.google.client_id') }}&redirect_uri={{ config('services.google.redirect') }}&response_type=code&scope=https://www.googleapis.com/auth/userinfo.profile openid https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/youtube https://www.googleapis.com/auth/youtube.readonly&access_type=offline">SignUp With Google</a>