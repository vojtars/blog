<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Model;

use Doctrine\ORM\QueryBuilder;
use Kdyby\Doctrine\EntityManager;

class SubscriberRepository extends BaseRepository
{

	/**
	 * SubscriberRepository constructor.
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		parent::__construct($entityManager->getRepository(Subscriber::class));
	}

	/**
	 * @param Blog   $blog
	 * @param string $email
	 * @return null|Subscriber
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function findByBlog(Blog $blog, string $email): ?Subscriber
	{
		return $this->createQueryBuilder('s')
			->where('s.blog = :blog')->setParameter('blog', $blog)
			->andWhere('s.email = :email')->setParameter('email', $email)
			->getQuery()->getOneOrNullResult();
	}

	/**
	 * @return QueryBuilder
	 */
	public function getDataGridQuery(): QueryBuilder
	{
		return $this->createQueryBuilder('s')
			->orderBy('s.dateAdd', 'DESC');
	}

	/**
	 * @param int $id
	 * @return Subscriber
	 * @throws SubscriberException
	 */
	public function getSubscriber(int $id): Subscriber
	{
		$subscriber = $this->find($id);
		return $this->checkResult($subscriber, new SubscriberException('Subscriber not found.'));
	}
}