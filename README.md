# Google and Github Login with Laravel Socialite 

How to add Google and Github login using Socialite in Laravel. Google One Tap with Vue and Laravel Socialite.

composer require laravel/socialite

## Google project

<https://console.cloud.google.com/projectcreate>

## Google OAuth consent screen

Create a consent screen for an app with permissions to:

- auth/userinfo.email
- auth/userinfo.profile
- openid

## Google oauth keys

Create external OAuth 2.0 client IDs and retrieve keys

<https://console.cloud.google.com/apis/credentials>

## Env

```sh
GOOGLE_CLIENT_ID=0000-xxxx.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=XXXX-vaI9hZo-mg
GOOGLE_REDIRECT_URL=https://localhost/google/callback
GOOGLE_HOME_URL=/
```

## Config

config/services.php

```sh
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URL'),
    'homepage' => env('GOOGLE_HOME_URL'),
]
```

## Routes

```php
<?php

use App\Http\Controllers\GoogleLoginController;
use Illuminate\Support\Facades\Route;

Route::get('/google/redirect', [GoogleLoginController::class, 'redirect'])->name('google.redirect');
Route::get('/google/callback', [GoogleLoginController::class, 'callback'])->name('google.callback');
Route::get('/google/logout', [GoogleLoginController::class, 'logout'])->name('google.logout');
Route::get('/google/oauth', [GoogleLoginController::class, 'oauth'])->name('google.oauth');
```

## Controller

```php
<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller
{
    public function logout()
    {
        Auth::logout();
        return redirect(config('services.google.homepage', '/'));
    }

    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();
        $user = User::where('email', $googleUser->email)->first();
        if (!$user) {
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'password' => \Hash::make(md5(uniqid() . microtime())),
                'email_verified_at' => now(),
            ]);
        }
        Auth::login($user);
        return redirect(config('services.google.homepage', '/'));

        // Allowed
        // $user->getId();
        // $user->getNickname();
        // $user->getName();
        // $user->getEmail();
        // $user->getAvatar();
    }

    public function oauth()
    {
        $token = request()->input('token'); // Jwt token from javascript
        $client = config('services.google.client_id');
        $res = Http::get("https://oauth2.googleapis.com/tokeninfo", ["id_token" => $token]);

        if ($res->ok()) {
            $arr = $res->json();
            if ($arr['aud'] != $client) {
                // Change to json if you need
                return redirect(config('services.google.homepage', '/'));
            }
            $user = User::where('email', $arr['email'])->first();
            if (!$user) {
                $user = User::create([
                    'name' => $arr['name'],
                    'email' => $arr['email'],
                    'password' => \Hash::make(md5(uniqid() . microtime())),
                    'email_verified_at' => now(),
                ]);
            }
            Auth::login($user);
        }
        // Change to json if you need
        return redirect(config('services.google.homepage', '/'));
    }
}
```

## Login button

```blade
<?php
@if (Auth::check())
    <div>{{ Auth::user()->name }}</div>
    <a href="{{ route('google.logout') }}">Logout</a>
@else
    <a href="{{ route('google.redirect') }}">Login with Google</a>
@endif
```

## Javascript Google One Tap and button (optional)

```html
@if (!Auth::check())
<div id="buttonDiv"></div>
<script src="https://accounts.google.com/gsi/client" async defer></script>
<script>
	function handleCredentialResponse(response) {
		window.location.href = '/google/redirect'
		// window.location.href = '/google/oauth?token=' + response.credential
		// Here we can do whatever process with the response we want
		// Note that response.credential is a JWT ID token
		// console.log("Encoded JWT ID token: " + response.credential);
	}
	
	window.onload = function () {
		google.accounts.id.initialize({
			client_id: "{{ config('services.google.client_id') }}", // Or replace with your Google Client ID
			callback: handleCredentialResponse // We choose to handle the callback in client side, so we include a reference to a function that will handle the response
		});

		// Show "Sign-in" button (optional)
		google.accounts.id.renderButton(document.getElementById("buttonDiv"),{ theme: "outline", size: "small" });
		// Display the One Tap dialog
		google.accounts.id.prompt();
		// Hide One Tap onclick
		const button = document.querySelector('body');
		// Event
		button.onclick = () => {
			google.accounts.id.disableAutoSelect();
			google.accounts.id.cancel();
		}
	}
</script>
@endif
```
