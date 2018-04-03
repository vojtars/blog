<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Model;


use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\QueryBuilder;

class HomepageRepository extends BaseRepository
{

	/**
	 * HomepageRepository constructor.
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		parent::__construct($entityManager->getRepository(Homepage::class));
	}

	/**
	 * @return array|Homepage[]
	 */
	public function getActiveBlocks(): array
	{
		return $this->createQueryBuilder('h')
			->where('h.active = :active')->setParameter('active', TRUE)
			->orderBy('h.position')
			->getQuery()->getResult();
	}

	/**
	 * @return QueryBuilder
	 */
	public function getDataGridQuery(): QueryBuilder
	{
		return $this->createQueryBuilder('h');
	}

	/**
	 * @param int $id
	 * @return Homepage
	 */
	public function getHomepageBlock(int $id): Homepage
	{
		$homepage = $this->find($id);
		return $this->checkResult($homepage, new NoHomepageException('Homepage block not found.'));
	}
}