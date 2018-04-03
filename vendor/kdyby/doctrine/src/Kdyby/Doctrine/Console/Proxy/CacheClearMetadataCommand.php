<?php

/**
 * This file is part of the Kdyby (http://www.kdyby.org)
 *
 * Copyright (c) 2008 Filip Procházka (filip@prochazka.su)
 *
 * For the full copyright and license information, please view the file license.txt that was distributed with this source code.
 */

namespace Kdyby\Doctrine\Console\Proxy;

use Doctrine\ORM\Tools\Console\Command\ClearCache\MetadataCommand;
use Kdyby\Doctrine\Console\OrmDelegateCommand;

/**
 * Command to clear the metadata cache of the various cache drivers.
 */
class CacheClearMetadataCommand extends OrmDelegateCommand
{

	protected function createCommand()
	{
		return new MetadataCommand();
	}

}
