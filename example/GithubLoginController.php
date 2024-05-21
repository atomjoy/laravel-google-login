<?php


namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;

class GithubLoginController extends Controller
{
	/**
	 * Logout
	 *
	 * @return void
	 */
	public function logout()
	{
		Auth::logout();

		return redirect(config('services.github.homepage', '/'));
	}

	/**
	 * Redirect to github
	 *
	 * @return void
	 */
	public function redirect()
	{
		return Socialite::driver('github')->redirect();
	}

	/**
	 * Callback from github
	 *
	 * @return void
	 */
	public function callback()
	{
		$githubUser = Socialite::driver('github')->stateless()->user();

		$user = User::where('email', $githubUser->email)->first();

		if (!$user) {
			$user = User::create([
				'name' => $githubUser->name,
				'email' => $githubUser->email,
				'password' => Hash::make(md5(uniqid() . microtime())),
				'email_verified_at' => now(),
			]);
		}

		Auth::login($user);

		return redirect(config('services.github.homepage', '/'));
	}
}
