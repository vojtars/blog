<?php declare(strict_types=1);
/**
 * Copyright (c) 2018. 
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Model;

class BlogManager
{
	/**
	 * @var BlogRepository
	 */
	private $blogRepository;


	/**
	 * BlogManager constructor.
	 * @param BlogRepository $blogRepository
	 */
	public function __construct(BlogRepository $blogRepository)
	{
		$this->blogRepository = $blogRepository;
	}

	/**
	 * @param int $id
	 * @return Blog
	 * @throws NoBlogException
	 */
	public function getBlog(int $id)
	{
		return $this->blogRepository->getBlog($id);
	}

	/**
	 * @return array
	 */
	public function getActiveBlogs(): array
	{
		return $this->blogRepository->getActiveBlogs();
	}
}