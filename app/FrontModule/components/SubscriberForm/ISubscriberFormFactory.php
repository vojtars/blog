<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace FrontModule\Components;

/**
 * Interface ISubscriberFormFactory
 */
interface ISubscriberFormFactory
{

	/**
	 * @return SubscriberForm
	 */
	public function create();
}