<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Model;

class CategoryManager
{
	/**
	 * @var CategoryRepository
	 */
	private $categoryRepository;
	/**
	 * @var PostRepository
	 */
	private $postRepository;

	/**
	 * CategoryManager constructor.
	 * @param CategoryRepository $categoryRepository
	 * @param PostRepository     $postRepository
	 */
	public function __construct(CategoryRepository $categoryRepository, PostRepository $postRepository)
	{
		$this->categoryRepository = $categoryRepository;
		$this->postRepository = $postRepository;
	}

	/**
	 * @param \Vojtars\Model\Blog $blog
	 * @return array
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function getActiveBlogCategories(Blog $blog): array
	{
		$categories =  $this->categoryRepository->getActiveBlogCategories($blog);
		$categoriesArray = [];
		/** @var Category $category */
		foreach ($categories as $category) {
			$categoriesArray[] = [
				'id'    => $category->getId(),
				'url'   => $category->getUrl(),
				'name'  => $category->getName(),
				'posts' => $this->postRepository->getCategoryActivePostsCount($category),
			];
		}
		return $categoriesArray;
	}

	/**
	 * @param string $url
	 * @return null|Category
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function getActiveCategoryByUrl(string $url)
	{
		return $this->categoryRepository->findByUrl($url);
	}

	/**
	 * @param int $id
	 * @return Category
	 * @throws CategoryException
	 */
	public function getCategory(int $id): Category
	{
		return $this->categoryRepository->getCategory($id);
	}

	/**
	 * @param string $url
	 * @return Category
	 * @throws CategoryException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function getCategoryByUrl(string $url): Category
	{
		return $this->categoryRepository->getCategoryByUrl($url);
	}
}