<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Model;

use Kdyby\Doctrine\EntityRepository;
use Tracy\Debugger;
use Tracy\ILogger;

class BaseRepository
{
	/**
	 * @var EntityRepository
	 */
	private $entityRepository;

	/**
	 * BaseRepository constructor.
	 * @param EntityRepository $entityRepository
	 */
	public function __construct(EntityRepository $entityRepository)
	{
		$this->entityRepository = $entityRepository;
	}

	/**
	 * @param string $alias
	 * @return \Kdyby\Doctrine\QueryBuilder
	 */
	public function createQueryBuilder(string $alias)
	{
		return $this->entityRepository->createQueryBuilder($alias);
	}

	/**
	 * @param int $id
	 * @return null|object
	 */
	public function find(int $id)
	{
		return $this->entityRepository->find($id);
	}

	public function findBy(array $criteria, array $orderBy = NULL, $limit = NULL, $offset = NULL)
	{
		return $this->entityRepository->findBy($criteria, $orderBy, $limit, $offset);
	}

	/**
	 * @param array      $criteria
	 * @param array|NULL $orderBy
	 * @return mixed|null|object
	 */
	public function findOneBy(array $criteria, array $orderBy = NULL)
	{
		return $this->entityRepository->findOneBy($criteria, $orderBy);
	}

	/**
	 * @return array
	 */
	public function findAll()
	{
		return $this->entityRepository->findAll();
	}

	/**
	 * @param array       $criteria
	 * @param string|NULL $value
	 * @param null|array|string $orderBy
	 * @param string|NULL $key
	 * @return array
	 */
	public function findPairs(array $criteria, string $value = NULL, $orderBy = NULL, string $key = NULL)
	{
		return $this->entityRepository->findPairs($criteria, $value, $orderBy, $key);
	}

	/**
	 * @param            $result
	 * @param \Exception $exception
	 * @return mixed
	 */
	public function checkResult($result, \Exception $exception)
	{
		if (empty($result)) {
			Debugger::log($exception, ILogger::ERROR);
			throw new $exception;
		} else {
			return $result;
		}
	}
}