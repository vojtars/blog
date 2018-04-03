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
use Nette\Utils\DateTime;
use Tracy\Debugger;

class PostRepository extends BaseRepository
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
		parent::__construct($entityManager->getRepository(Post::class));
		$this->entityManager = $entityManager;
	}

	public function getDataGridQuery(Blog $blog): QueryBuilder
	{
		return $this->createQueryBuilder('p')
			->where('p.blog = :blog')->setParameter('blog', $blog)
			->andWhere('p.deleted = :deleted')->setParameter('deleted', FALSE);
	}

	/**
	 * @param Blog     $blog
	 * @param int|null $count
	 * @return array|Blog[]
	 */
	public function getPostList(Blog $blog, int $count = NULL): array
	{
		$query = $this->createQueryBuilder('p')
			->leftJoin('p.blog', 'b')
			->leftJoin('p.category', 'c')
			->where('b.active = :active')
			->andWhere('c.active = :active')->setParameter('active', TRUE)
			->andWhere('p.blog = :blog')->setParameter('blog', $blog)
			->andWhere('p.public = :public')->setParameter('public', TRUE)
			->andWhere('p.publicDate < :now')->setParameter('now', new DateTime());

		if (!empty($count)) {
			$query->setMaxResults($count);
		}

		return $query->orderBy('p.publicDate', 'DESC')->getQuery()->getResult();
	}

	/**
	 * @param string $postUrl
	 * @return null|Post
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function findByUrl(string $postUrl): ?Post
	{
		return $this->createQueryBuilder('p')
			->leftJoin('p.blog', 'b')
			->leftJoin('p.category', 'c')
			->where('b.active = :active')
			->andWhere('c.active = :active')->setParameter('active', TRUE)
			->andWhere('p.url = :postUrl')->setParameter('postUrl', $postUrl)
			->andWhere('p.public = :public')->setParameter('public', TRUE)
			->andWhere('p.publicDate < :now')->setParameter('now', new \DateTime())
			->getQuery()->getOneOrNullResult();
	}

	/**
	 * @param int $count
	 * @return array|Blog[]
	 */
	public function getLastPosts(int $count = 10): array
	{
		return $this->createQueryBuilder('p')
			->leftJoin('p.blog', 'b')
			->leftJoin('p.category', 'c')
			->where('b.active = :active')
			->andWhere('c.active = :active')->setParameter('active', TRUE)
			->andWhere('p.public = :public')->setParameter('public', TRUE)
			->andWhere('p.publicDate < :now')->setParameter('now', new DateTime())
			->orderBy('p.publicDate', 'DESC')
			->setMaxResults($count)
			->getQuery()->getResult();
	}

	/**
	 * @param \Vojtars\Model\Post $post
	 * @param int                 $count
	 * @return array
	 */
	public function getNextList(Post $post, int $count = 2): array
	{
		$query = $this->createQueryBuilder('p')
			->leftJoin('p.blog', 'b')
			->leftJoin('p.category', 'c')
			->where('b.active = :active')
			->andWhere('c.active = :active')->setParameter('active', TRUE)
			->andWhere('p.blog = :blog')->setParameter('blog', $post->getBlog())
			->andWhere('p.public = :public')->setParameter('public', TRUE)
			->andWhere('p.id <> :postId')->setParameter('postId', $post->getId())
			->andWhere('p.publicDate < :now')->setParameter('now', new DateTime());

		if (!empty($count)) {
			$query->setMaxResults($count);
		}

		return $query->orderBy('p.publicDate', 'DESC')->getQuery()->getResult();
	}

	/**
	 * @param int $id
	 * @return Post
	 * @throws NoPostException
	 */
	public function getPost(int $id): Post
	{
		$post = $this->find($id);
		return $this->checkResult($post, new NoPostException('Post not found'));
	}

	/**
	 * @param Category $category
	 * @return int
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function getCategoryActivePostsCount(Category $category): int
	{

		return (int)$this->createQueryBuilder('p')
			->select(['COUNT(p.id)'])
			->where('p.category = :category')->setParameter('category', $category)
			->andWhere('p.public = :public')->setParameter('public', TRUE)
			->andWhere('p.publicDate < :now')->setParameter('now', new \DateTime())
			->getQuery()->getSingleScalarResult();

	}

	/**
	 * @param Category $category
	 * @param int|NULL $count
	 * @return array
	 */
	public function getCategoryPostList(Category $category, int $count = NULL): array
	{
		$query = $this->createQueryBuilder('p')
			->leftJoin('p.blog', 'b')
			->leftJoin('p.category', 'c')
			->where('b.active = :active')
			->andWhere('c.active = :active')->setParameter('active', TRUE)
			->andWhere('p.category = :category')->setParameter('category', $category)
			->andWhere('p.public = :public')->setParameter('public', TRUE)
			->andWhere('p.publicDate < :now')->setParameter('now', new DateTime());

		if (!empty($count)) {
			$query->setMaxResults($count);
		}

		return $query->orderBy('p.publicDate', 'DESC')->getQuery()->getResult();
	}

	/**
	 * @param Category $category
	 * @param string   $query
	 * @return array
	 */
	public function searchInCategory(Category $category, string $query): array
	{
		return $this->createQueryBuilder('p')
			->leftJoin('p.blog', 'b')
			->leftJoin('p.category', 'c')
			->where('b.active = :active')
			->andWhere('c.active = :active')->setParameter('active', TRUE)
			->andWhere('p.category = :category')->setParameter('category', $category)
			->andWhere('p.name LIKE :searchString OR p.description LIKE :searchString OR p.content LIKE :searchString')
			->setParameter('searchString', '%'.$query.'%')
			->andWhere('p.public = :public')->setParameter('public', TRUE)
			->andWhere('p.publicDate < :now')->setParameter('now', new DateTime())
			->orderBy('p.publicDate', 'DESC')->getQuery()->getResult();
	}


	/**
	 * @param Blog   $blog
	 * @param string $query
	 * @return array
	 */
	public function searchInBlog(Blog $blog, string $query): array
	{
		return $this->createQueryBuilder('p')
			->leftJoin('p.blog', 'b')
			->leftJoin('p.category', 'c')
			->where('b.active = :active')
			->andWhere('c.active = :active')->setParameter('active', TRUE)
			->andWhere('p.blog = :blog')->setParameter('blog', $blog)
			->andWhere('p.name LIKE :searchString OR p.description LIKE :searchString OR p.content LIKE :searchString')
			->setParameter('searchString', '%'.$query.'%')
			->andWhere('p.public = :public')->setParameter('public', TRUE)
			->andWhere('p.publicDate < :now')->setParameter('now', new DateTime())
			->orderBy('p.publicDate', 'DESC')->getQuery()->getResult();
	}

	/**
	 * @param string $url
	 * @return Post
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 * @throws NoPostException
	 */
	public function getByUrl(string $url): Post
	{
		$post = $this->createQueryBuilder('p')
			->where('p.url = :url')->setParameter('url', $url)
			->getQuery()->getOneOrNullResult();

		return $this->checkResult($post, new NoPostException('Post no found.'));
	}

	/**
	 * @param Blog $blog
	 * @return QueryBuilder
	 */
	public function getDataGridQueryForDashboard(Blog $blog): QueryBuilder
	{
		return $this->createQueryBuilder('p')
			->where('p.deleted = :deleted')->setParameter('deleted', FALSE)
			->andWhere('p.blog = :blog')->setParameter('blog', $blog)
			->orderBy('p.dateEdit', 'DESC');
	}

	/**
	 * @param string $searchQuery
	 * @return array|Post[]
	 */
	public function searchInAllPosts(string $searchQuery): array
	{
		return $this->createQueryBuilder('p')
			->leftJoin('p.blog', 'b')
			->leftJoin('p.category', 'c')
			->where('b.active = :active')
			->andWhere('c.active = :active')->setParameter('active', TRUE)
			->andWhere('p.name LIKE :searchString OR p.description LIKE :searchString OR p.content LIKE :searchString')
			->setParameter('searchString', '%'.$searchQuery.'%')
			->andWhere('p.public = :public')->setParameter('public', TRUE)
			->andWhere('p.publicDate < :now')->setParameter('now', new DateTime())
			->orderBy('p.publicDate', 'DESC')->getQuery()->getResult();
	}


}
