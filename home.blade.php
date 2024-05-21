<?php
@if (Auth::check())
    <div>{{ Auth::user()->name }}</div>
    <a href="{{ route('google.logout') }}">Logout</a>
@else
    <a href="{{ route('google.redirect') }}">Login with Google</a>
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
    client_id: "Google_Client_ID", // Replace with your Google Client ID
    callback: handleCredentialResponse // We choose to handle the callback in client side, so we include a reference to a function that will handle the response
   });
   // Show "Sign-in" button (optional)
   google.accounts.id.renderButton(document.getElementById("buttonDiv"),{ theme: "outline", size: "small" });
   // Display the One Tap dialog
   google.accounts.id.prompt();
   // Hide One Tap onclick
   const button = document.querySelector('body');
   button.onclick = () => {
    google.accounts.id.disableAutoSelect();
    google.accounts.id.cancel();
   }
  }
</script>
@endif
