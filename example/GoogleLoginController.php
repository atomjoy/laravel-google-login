<?php


namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;

class GoogleLoginController extends Controller
{
	/**
	 * Logout
	 *
	 * @return void
	 */
	public function logout()
	{
		Auth::logout();

		return redirect(config('services.google.homepage', '/'));
	}

	/**
	 * Redirect to google
	 *
	 * @return void
	 */
	public function redirect()
	{
		return Socialite::driver('google')->redirect();
	}

	/**
	 * Callback from google
	 *
	 * @return void
	 */
	public function callback()
	{
		$googleUser = Socialite::driver('google')->stateless()->user();

		$user = User::where('email', $googleUser->email)->first();

		if (!$user) {
			$user = User::create([
				'name' => $googleUser->name,
				'email' => $googleUser->email,
				'password' => Hash::make(md5(uniqid() . microtime())),
				'email_verified_at' => now(),
			]);
		}	

		Auth::login($user);

		return redirect(config('services.google.homepage', '/'));
	}

	public function oauth()
	{
		$token = request()->input('token');
		$client = config('services.google.client_id');

		$res = Http::get("https://oauth2.googleapis.com/tokeninfo", ["id_token" => $token]);

		if ($res->ok()) {
			$arr = $res->json();
			if ($arr['aud'] != $client) {
				return redirect(config('services.google.homepage', '/'));
			}
			$user = User::where('email', $arr['email'])->first();
			if (!$user) {
				$user = User::create([
					'name' => $arr['name'],
					'email' => $arr['email'],
					'password' => Hash::make(md5(uniqid() . microtime())),
					'email_verified_at' => now(),
				]);
			}
			Auth::login($user);
		}
		return redirect(config('services.google.homepage', '/'));
	}
}
