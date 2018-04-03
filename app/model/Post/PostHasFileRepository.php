<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Model;


use Kdyby\Doctrine\EntityManager;

class PostHasFileRepository extends BaseRepository
{

	/**
	 * PostHasFileRepository constructor.
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		parent::__construct($entityManager->getRepository(PostHasFile::class));
	}

	/**
	 * @param Post $post
	 */
	public function removeAllPostFiles(Post $post)
	{
		$this->createQueryBuilder('phf')
			->delete()
			->where('phf.post = :post')->setParameter('post', $post)
			->getQuery()->execute();
	}

	public function findPostFiles(Post $post)
	{
		return $this->createQueryBuilder('phf')
			->where('phf.post = :post')->setParameter('post', $post)
			->getQuery()->getResult();
	}


}