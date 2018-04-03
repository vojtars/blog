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

class UserGrid extends Control
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
		$this->setTemplateName('default.latte');

	}

	/**
	 * @throws \ReflectionException
	 */
	public function render()
	{
		$template = $this->getTemplate();
		$template->setFile($this->getTemplateFullPath());
		$template->render();
	}

	/**
	 * @return DataGrid
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
	 */
	public function createComponentUserGrid()
	{
		$grid = new DataGrid();

		$grid->setDataSource($this->userRepository->getDataGridQuery());
		$grid->setDefaultPerPage(20);
		$grid->setItemsPerPageList([20, 50, 100, 200]);
		$grid->setRememberState(FALSE);

		$grid->addColumnText('id', 'Id')
			->setSortable();

		$grid->addColumnText('name', 'Jméno')
			->setSortable()
			->setEditableCallback(function ($id, $value) {
				/** @var User $user */
				$user = $this->userRepository->find((int)$id);
				$user->setName($value);
				$this->entityManager->flush($user);
			});

		$grid->addColumnText('surname', 'Přijmení')
			->setSortable()
			->setEditableCallback(function ($id, $value) {
				/** @var User $user */
				$user = $this->userRepository->find((int)$id);
				$user->setSurname($value);
				$this->entityManager->flush($user);
			});

		$grid->addColumnText('email', 'Email')
			->setSortable()
			->setEditableCallback(function ($id, $value) {
				/** @var User $user */
				$user = $this->userRepository->find((int)$id);
				$user->setEmail($value);
				$this->entityManager->flush($user);
			});

		$grid->addColumnText('password', 'Heslo')
			->setSortable()
			->setEditableCallback(function ($id, $value) {
				/** @var User $user */
				$user = $this->userRepository->find((int)$id);
				$user->setPassword(Passwords::hash($value));
				$this->entityManager->flush($user);
			});

		$grid->addColumnText('dateAdd', 'Datum Registrace')
			->setSortable()
			->setRenderer(function (User $user) {
				if (empty($user->getDateAdd())) {
					return '---';
				} else {
					return $user->getDateAdd()->format('d.m.Y H:i:s');
				}
			});

		$grid->addColumnText('dateLastLogin', 'Naposledy přihlášen')
			->setSortable()
			->setRenderer(function (User $user) {
				if (empty($user->getDateLastLogin())) {
					return '---';
				} else {
					return $user->getDateLastLogin()->format('d.m.Y H:i:s');
				}
			});

		return $grid;
	}


}