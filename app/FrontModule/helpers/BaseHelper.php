<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Helpers;

use Nette\SmartObject;

/**
 * Základní třída helperů, slouží jako loader, který zaregistruje všechny potomky.
 */
class BaseHelper
{

	use SmartObject;

	/**
	 * @param $helper
	 * @return mixed
	 */
	public function loader($helper)
	{
		$arg = func_get_args();
		$func = array_shift($arg);

		if (method_exists($this, $helper)) {
			return call_user_func_array(array($this, $helper), $arg);
		}
	}

}
