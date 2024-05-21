# Google and Github Login with Laravel Socialite 

How to add Google and Github login using Socialite in Laravel. Google One Tap with Vue and Laravel Socialite.

## Install

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

## Github new app oauth keys

<https://github.com/settings/developers>

## Setings

.env file

```sh
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URL=https://localhost/google/callback
GOOGLE_HOME_URL=/

GITHUB_CLIENT_ID=
GITHUB_CLIENT_SECRET=
GITHUB_REDIRECT_URL=https://localhost/github/callback
GITHUB_HOME_URL=/
```

## Config

config/services.php

```sh
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URL'),
    'homepage' => env('GOOGLE_HOME_URL'),
],

'github' => [
	'client_id' => env('GITHUB_CLIENT_ID'),
	'client_secret' => env('GITHUB_CLIENT_SECRET'),
	'redirect' => env('GITHUB_REDIRECT_URL'),
	'homepage' => env('GITHUB_HOME_URL'),
],
```

## Routes

```php
<?php

use App\Http\Controllers\GoogleLoginController;
use Illuminate\Support\Facades\Route;

# Google
Route::get('/google/redirect', [GoogleLoginController::class, 'redirect'])->name('google.redirect');
Route::get('/google/callback', [GoogleLoginController::class, 'callback'])->name('google.callback');
Route::get('/google/logout', [GoogleLoginController::class, 'logout'])->name('google.logout');
Route::get('/google/oauth', [GoogleLoginController::class, 'oauth'])->name('google.oauth');

# Github
Route::get('/github/redirect', [GithubLogin::class, 'redirect'])->name('github.redirect');
Route::get('/github/callback', [GithubLogin::class, 'callback'])->name('github.callback');
Route::get('/github/logout', [GithubLogin::class, 'logout'])->name('github.logout');
```

## Controllers

Copy controllers to app/Http/Controllers

## Login button

```blade
<?php
@if (Auth::check())
    <div>{{ Auth::user()->name }}</div>
    <a href="{{ route('google.logout') }}">Logout</a>
@else
    <a href="{{ route('google.redirect') }}">Login with Google</a>
    <a href="{{ route('github.redirect') }}">Login with Github</a>
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
