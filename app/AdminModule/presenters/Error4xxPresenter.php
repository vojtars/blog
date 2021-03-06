<?php declare(strict_types=1);
/**
 * Copyright (c) 2018. 
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace AdminModule\Presenters;

use Nette;


class Error4xxPresenter extends BasePresenter
{

	/**
	 * @throws \Nette\Application\AbortException
	 * @throws \Nette\Application\BadRequestException
	 * @throws \Nette\InvalidStateException
	 */
	public function startup()
	{
		parent::startup();
		if (!$this->getRequest()->isMethod(Nette\Application\Request::FORWARD)) {
			$this->error();
		}
	}

	/**
	 * @param Nette\Application\BadRequestException $exception
	 */
	public function renderDefault(Nette\Application\BadRequestException $exception)
	{
		// load template 403.latte or 404.latte or ... 4xx.latte
		$file = __DIR__ . "/templates/Error4xx/{$exception->getCode()}.latte";
		$this->template->setFile(is_file($file) ? $file : __DIR__ . '/templates/Error4xx/4xx.latte');
	}

}
