<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace AdminModule\Components;

/**
 * Interface IBlogFormFactory
 */
interface IBlogFormFactory
{

	/**
	 * @return BlogForm
	 */
	public function create();
}