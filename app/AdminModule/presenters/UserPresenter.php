<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace AdminModule\Presenters;

use AdminModule\Components\IUserFormFactory;
use AdminModule\Components\IUserGridFactory;
use AdminModule\Components\UserForm;
use AdminModule\Components\UserGrid;

class UserPresenter extends BasePresenter
{
	/**
	 * @var IUserGridFactory
	 */
	private $userGridFactory;

	/**
	 * @var IUserFormFactory
	 */
	private $userFormFactory;


	/**
	 * UserPresenter constructor.
	 * @param IUserGridFactory $userGridFactory
	 * @param IUserFormFactory $userFormFactory
	 */
	public function __construct(IUserGridFactory $userGridFactory, IUserFormFactory $userFormFactory)
	{
		parent::__construct();
		$this->userGridFactory = $userGridFactory;
		$this->userFormFactory = $userFormFactory;
	}

	public function renderDefault()
	{
		$this->template->anyVariable = 'any value';
	}

	/**
	 * @return \AdminModule\Components\UserGrid
	 */
	public function createComponentUserGrid(): UserGrid
	{
		return $this->userGridFactory->create();
	}

	/**
	 * @return \AdminModule\Components\UserForm
	 */
	public function createComponentUserForm(): UserForm
	{
		return $this->userFormFactory->create();
	}
}
