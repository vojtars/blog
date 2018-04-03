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

class BlogRepository extends BaseRepository
{
	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * CategoryRepository constructor.
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		parent::__construct($entityManager->getRepository(Blog::class));
		$this->entityManager = $entityManager;
	}

	/**
	 * @return array
	 */
	public function getActiveBlogs(): array
	{
		return $this->createQueryBuilder('b')
			->where('b.active = 1')
			->getQuery()->getResult();
	}

	/**
	 * @return QueryBuilder
	 */
	public function getDataGridQuery(): QueryBuilder
	{
		return $this->createQueryBuilder('b');
	}

	/**
	 * @param string $blogUrl
	 * @return null|Blog
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function findByUrl(string $blogUrl): ?Blog
	{
		return $this->createQueryBuilder('b')
			->where('b.url = :url')->setParameter('url', $blogUrl)
			->getQuery()->getOneOrNullResult();
	}

	/**
	 * @param string $blogUrl
	 * @return null|Blog
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function findActiveByUrl(string $blogUrl): ?Blog
	{
		return $this->createQueryBuilder('b')
			->where('b.url = :url')->setParameter('url', $blogUrl)
			->andWhere('b.active = :active')->setParameter('active', TRUE)
			->getQuery()->getOneOrNullResult();
	}

	/**
	 * @return array
	 */
	public function getActiveBlogsForSelectInput()
	{
		return $this->findPairs(['active' => TRUE], 'name');
	}

	/**
	 * @param int $blogId
	 * @return Blog
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 * @throws NoBlogException
	 */
	public function getActiveBlog(int $blogId): Blog
	{
		$blog = $this->createQueryBuilder('b')
			->where('b.active = :active')->setParameter('active', TRUE)
			->andWhere('b.id = :id')->setParameter('id', $blogId)
			->getQuery()->getOneOrNullResult();

		return $this->checkResult($blog, new NoBlogException('Blog not found'));
	}

	public function getDefaultBlog()
	{
	}

	/**
	 * @param string $url
	 * @return Blog
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 * @throws NoBlogException
	 */
	public function getBlogByUrl(string $url): Blog
	{
		$blog = $this->createQueryBuilder('b')
			->where('b.url = :url')->setParameter('url', $url)
			->getQuery()->getOneOrNullResult();

		return $this->checkResult($blog, new NoBlogException('Blog not found'));
	}

	/**
	 * @param int $id
	 * @return Blog
	 * @throws NoBlogException
	 */
	public function getBlog(int $id): Blog
	{
		$blog = $this->find($id);
		return $this->checkResult($blog, new NoBlogException('Blog not found'));
	}


}