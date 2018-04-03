<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Model;

use Kdyby\Doctrine\EntityManager;
use Tracy\Debugger;
use Tracy\ILogger;

/**
 * UserRepository
 *
 */
class UserRepository
{
	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var \Kdyby\Doctrine\EntityRepository
	 */
	private $userRepository;

	/**
	 * UserRepository constructor.
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
		$this->userRepository = $this->entityManager->getRepository(User::class);
	}

	/**
	 * @param int $id
	 * @return null|object
	 */
	public function find(int $id)
	{
		return $this->userRepository->find($id);
	}

	/**
	 * @return \Kdyby\Doctrine\QueryBuilder
	 */
	public function getDataGridQuery()
	{
		return $this->userRepository->createQueryBuilder('u');
	}
}
