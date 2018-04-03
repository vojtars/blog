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

class CategoryRepository extends BaseRepository
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
		parent::__construct($entityManager->getRepository(Category::class));
		$this->entityManager = $entityManager;
	}

	/**
	 * @param Blog $blog
	 * @return QueryBuilder
	 */
	public function getDataGridQuery(Blog $blog): QueryBuilder
	{
		return $this->createQueryBuilder('c')
			->where('c.blog = :blog')->setParameter('blog', $blog);
	}

	/**
	 * @param Blog $blog
	 * @return array|null
	 */
	public function getCategoriesForSelect(Blog $blog): ?array
	{
		$query = $this->getActiveBlogCategories($blog);

		if (empty($query)) {
			return NULL;
		} else {
			$select[0] = 'Vyberte';
			/** @var Category $category */
			foreach ($query as $category) {
				$select[$category->getId()] = $category->getName();
			}
			return $select;
		}
	}

	/**
	 * @param \Vojtars\Model\Blog $blog
	 * @return array
	 */
	public function getActiveBlogCategories(Blog $blog): array
	{
		return $this->createQueryBuilder('c')
			->where('c.blog = :blog')->setParameter('blog', $blog)
			->andWhere('c.active = :active')->setParameter('active', TRUE)
			->orderBy('c.name')
			->getQuery()->getResult();
	}

	/**
	 * @param string $url
	 * @return null|Category
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function findByUrl(string $url): ?Category
	{
		return $this->createQueryBuilder('c')
			->where('c.url = :url')->setParameter('url', $url)
			->andWhere('c.active = :active')->setParameter('active', TRUE)
			->getQuery()->getOneOrNullResult();
	}

	/**
	 * @param int $id
	 * @return Category
	 * @throws CategoryException
	 */
	public function getCategory(int $id): Category
	{
		$category = $this->find($id);
		return $this->checkResult($category, new CategoryException());
	}

	/**
	 * @param string $url
	 * @return null|Category
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 * @throws CategoryException
	 */
	public function getCategoryByUrl(string $url): Category
	{
		$category = $this->createQueryBuilder('c')
			->where('c.url = :url')->setParameter('url', $url)
			->getQuery()->getOneOrNullResult();

		return $this->checkResult($category, new CategoryException());
	}
}