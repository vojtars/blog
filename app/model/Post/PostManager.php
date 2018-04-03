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

class PostManager
{

	/**
	 * @var \Vojtars\Model\PostRepository
	 */
	private $postRepository;

	/**
	 * @var \Kdyby\Doctrine\EntityManager
	 */
	private $entityManager;

	/**
	 * PostManager constructor.
	 * @param \Vojtars\Model\PostRepository $postRepository
	 * @param \Kdyby\Doctrine\EntityManager $entityManager
	 */
	public function __construct(PostRepository $postRepository, EntityManager $entityManager)
	{
		$this->postRepository = $postRepository;
		$this->entityManager = $entityManager;
	}

	/**
	 * @param string $url
	 * @return null|\Vojtars\Model\Post
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function findByUrl(string $url): ?Post
	{
		return $this->postRepository->findByUrl($url);
	}

	/**
	 * @param \Vojtars\Model\Blog $blog
	 * @return array|\Vojtars\Model\Blog[]
	 */
	public function getPostList(Blog $blog): array
	{
		return $this->postRepository->getPostList($blog);
	}

	/**
	 * @param \Vojtars\Model\Post $post
	 * @param int                 $count
	 * @return array|\Vojtars\Model\Blog[]
	 */
	public function getNextPosts(Post $post, int $count = 2): array
	{
		return $this->postRepository->getNextList($post, $count);
	}

	/**
	 * @param \Vojtars\Model\Post $post
	 * @throws \Exception
	 */
	public function addStatistics(Post $post): void
	{
		$post->addView();
		$this->entityManager->flush($post);
	}

	/**
	 * @param int $id
	 * @return Post
	 * @throws NoPostException
	 */
	public function getPost(int $id): Post
	{
		return $this->postRepository->getPost($id);
	}

	/**
	 * @param Category $category
	 * @return array
	 */
	public function getCategoryPostList(Category $category): array
	{
		return $this->postRepository->getCategoryPostList($category);
	}

	/**
	 * @param Category $category
	 * @param string   $query
	 * @return mixed
	 */
	public function searchInCategoryPosts(Category $category, string $query)
	{
		return $this->postRepository->searchInCategory($category, $query);
	}

	public function searchInBlogPosts(Blog $blog, string $query)
	{
		return $this->postRepository->searchInBlog($blog, $query);
	}

	/**
	 * @param string $url
	 * @return Post
	 * @throws NoPostException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function getPostByUrl(string $url): Post
	{
		return $this->postRepository->getByUrl($url);
	}

	/**
	 * @param Blog $blog
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getDataGridQueryForDashboard(Blog $blog): QueryBuilder
	{
		return $this->postRepository->getDataGridQueryForDashboard($blog);
	}

	/**
	 * @param string|NULL $searchQuery
	 * @return array|null
	 */
	public function searchInAllPosts(string $searchQuery = NULL): ?array
	{
		return empty($searchQuery) ? NULL : $this->postRepository->searchInAllPosts($searchQuery);
	}


}