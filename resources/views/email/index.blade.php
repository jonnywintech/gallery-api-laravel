<h1>Email verification</h1>


Hello {{$user->name}}
<p>
    You registered an account, before being able to use your account
    <br>
    you need to verify that this is your email address by clicking here: <a href="{{
        env('APP_URL'). '/email/verify/'. $user->id
    }}">link</a></p>

<p>Kind Regards</p>
