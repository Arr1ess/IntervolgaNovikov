<?php
namespace app\router;

class CSRF
{
	const SESSION_KEY = 'csrf_token';

	public static function generateToken()
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}

		$token = bin2hex(random_bytes(32));
		self::saveTokenToSession($token);
		return $token;
	}

	private static function saveTokenToSession($token)
	{
		$_SESSION[self::SESSION_KEY] = $token;
	}

	public static function getTokenToForm()
	{
		return "'" . self::SESSION_KEY . "'" . ":" . "'" . password_hash(self::getTokenFromSession(), PASSWORD_DEFAULT)  . "'";
	}

	public static function getToken(){
		return password_hash(self::getTokenFromSession(), PASSWORD_DEFAULT);
	}


	public static function getTokenFromSession()
	{
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}

		return $_SESSION[self::SESSION_KEY] ?? self::generateToken();
	}

	public static function checkToken(): bool
	{
		$headers = apache_request_headers();
		if (!$headers || !isset($headers[self::SESSION_KEY])) {
			return false;
		}

		$token = $headers[self::SESSION_KEY];
		$storedToken = self::getTokenFromSession();
		// Logger::createDebugLog(" ". $storedToken ." ". $token);
		return password_verify($storedToken, $token);
	}
}