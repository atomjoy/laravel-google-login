<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scrollbar-thin" data-theme="dark">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Laravel Vue Socialite</title>

	<meta name="description" content="Laravel 11 Vue 3.4 Socialite Google Login.">
	<meta name="keywords" content="laravel, vue, socialite">
	<link rel="canonical" href="/" />

	<!-- Css, links -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

	@vite('resources/css/app.css')

	<style>
		.google-button {
			position: fixed;
			bottom: 20px;
			left: 20px;
			padding: 10px 20px;
			color: #0a0a0a;
			background: #fff;
			border-radius: 50px;
			border: 1px solid #e7e7e7;
			/* box-shadow: 0px 0px 5px #0001 */
		}

		.google-button img {
			float: left;
			margin-right: 20px;
			width: 25px;
		}

		.google-button span {
			float: left;
			font-size: 15px;
			font-weight: 600;
			font-family: Poppins
		}
	</style>
</head>

<body>
	<div id="app"></div>
	@vite('resources/js/app.js')

	@if (Auth::check())
	    {{ Auth::user()->name }}
	    <a href="{{ route('google.logout') }}" id="glogout">Logout</a>
	@else
	<a href="{{ route('google.redirect') }}" title="Google Login">
		<div class="google-button">
			<img src="storage/icons8-google-96.png" alt="Login with Google" width="48">
			<span> Sign in with Google </span>
		</div>
	</a>
	@endif

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
				client_id: "{{ config('services.google.client_id') }}", // Replace with your Google Client ID
				callback: handleCredentialResponse // We choose to handle the callback in client side, so we include a reference to a function that will handle the response
			});
			// Enable Google "Sign-in" button (optional)
			// google.accounts.id.renderButton(document.getElementById("buttonDiv"),{ theme: "outline", size: "small" });
			google.accounts.id.prompt(); // Display the One Tap dialog
			// Hide onetap
			const button = document.querySelector('body');
			button.onclick = () => {
				google.accounts.id.disableAutoSelect();
				google.accounts.id.cancel();
			}
		}
	</script>
	@endif
</body>

</html>
