<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Services;

class AppService
{
	const LOG_EMAIL = 'error@vojtars.cz';
	const LOCALHOST_IPV4 = '127.0.0.1';
	const LOCALHOST_IPV6 = '::1';

	/** @var [] */
	public static $debugIp = [self::LOCALHOST_IPV4, self::LOCALHOST_IPV6];

	public static function isDevel()
	{
		return isset($_SERVER['REMOTE_ADDR']) && in_array($_SERVER['REMOTE_ADDR'], self::$debugIp);
	}

}