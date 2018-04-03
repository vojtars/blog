<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Services;

class StringService
{

	/**
	 * Generates a random password with the given length and of the given type.
	 *
	 * @param int    $length
	 * @param string $type
	 * @return string | null
	 */
	public static function random_password($length = 8, $type = 'alpha_numeric')
	{
		if ($length < 1 || $length > 1024)
			return NULL;

		$lower = 'abcdefghijklmnopqrstuvwxy';
		$upper = strtoupper($lower);
		$numbers = '1234567890';
		$dash = '-';
		$underscore = '_';
		$symbols = '`~!@#$%^&*()+=[]\\{}|:";\'<>?,./';

		switch ($type) {
			case 'lower':
				$chars = $lower;
				break;
			case 'upper':
				$chars = $upper;
				break;
			case 'numeric':
				$chars = $numbers;
				break;
			case 'alpha':
				$chars = $lower . $upper;
				break;
			case 'symbol':
				$chars = $symbols . $dash . $underscore;
				break;
			case 'alpha_numeric':
				$chars = $lower . $upper . $numbers;
				break;
			case 'alpha_numeric_dash':
				$chars = $lower . $upper . $numbers . $dash;
				break;
			case 'alpha_numeric_underscore':
				$chars = $lower . $upper . $numbers . $underscore;
				break;
			case 'alpha_numeric_dash_underscore':
				$chars = $lower . $upper . $numbers . $underscore . $dash;
				break;
			case 'all':
				$chars = $lower . $upper . $numbers . $underscore . $dash . $symbols;
				break;
			default:
				return NULL;
		}

		$min = 0;
		$max = strlen($chars) - 1;

		$password = '';

		for ($i = 0; $i < $length; $i++) {
			$random = mt_rand($min, $max);
			$char = substr($chars, $random, 1);
			$password .= $char;
		}

		return $password;
	}
}