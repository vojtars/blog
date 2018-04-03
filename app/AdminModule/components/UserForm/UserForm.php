<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace AdminModule\Components;

use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;
use Nette\Application\UI\ITemplate;
use Nette\Security\Passwords;
use Tracy\Debugger;
use Ublaboo\DataGrid\DataGrid;
use Vojtars\Model\User;
use Vojtars\Model\UserRepository;

class UserForm extends Control
{
	use OwnTemplate;

	/**
	 * @var array
	 */
	public $onChange = [];

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var UserRepository
	 */
	private $userRepository;


	/**
	 * UserGrid constructor.
	 * @param EntityManager  $entityManager
	 * @param UserRepository $userRepository
	 */
	public function __construct(EntityManager $entityManager, UserRepository $userRepository)
	{
		parent::__construct();
		$this->entityManager = $entityManager;
		$this->userRepository = $userRepository;
		$this->setTemplateName('userForm.latte');

	}

	/**
	 * @throws \Nette\UnexpectedValueException
	 */
	public function render()
	{
		$template = $this->getTemplate();
		$template->setFile($this->getTemplateFullPath());
		$template->render();
	}
}