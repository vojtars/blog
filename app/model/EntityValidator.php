<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Model;

use Tracy\Debugger;

trait EntityValidator
{

	/**
	 * @param \Vojtars\Model\Image $image
	 * @return null|\Vojtars\Model\Image
	 */
	public function checkImage(Image $image = NULL): ?Image
	{
		if (empty($image)) {
			return NULL;
		} else {
			$url = "https://$_SERVER[HTTP_HOST]/" . $image->getNameWithPath();
			$headers = @get_headers($url, 1);
			if (isset($headers['Content-Type'])) {
				if (strpos($headers['Content-Type'], 'image/') === FALSE) {
					return NULL;
				} else {
					return $image;
				}
			} else {
				return $image;
			}
		}
	}
}
