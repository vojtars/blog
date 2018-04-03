<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace FrontModule\Presenters;

use Tracy\Debugger;
use Vojtars\Model\CategoryManager;
use Vojtars\Model\ImageManager;
use Vojtars\Model\Post;
use Vojtars\Model\PostManager;

class BlogPresenter extends BasePresenter
{
	
	/**
	 * @var Post
	 */
	private $post;

	/**
	 * @var \Vojtars\Model\CategoryManager
	 */
	private $categoryManager;
	/**
	 * @var ImageManager
	 */
	private $imageManager;

	/**
	 * BlogPresenter constructor.
	 * @param \Vojtars\Model\PostManager     $postManager
	 * @param \Vojtars\Model\CategoryManager $categoryManager
	 * @param ImageManager                   $imageManager
	 */
	public function __construct(PostManager $postManager, CategoryManager $categoryManager, ImageManager $imageManager )
	{
		parent::__construct();
		$this->postManager = $postManager;
		$this->categoryManager = $categoryManager;
		$this->imageManager = $imageManager;
	}

	/**
	 * @param string $url
	 * @param string $catUrl
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 * @throws \Nette\Application\BadRequestException
	 * @throws \Nette\Application\UI\InvalidLinkException
	 */
	public function actionDefault(string $url, string $catUrl = NULL)
	{
		$this->blog = $this->blogRepository->findActiveByUrl($url);
		if (empty($this->blog))
			$this->error('Blog not found');

		if (empty($catUrl)) {
			$this->editLink = $this->link(':Admin:Post:list', $this->blog->getUrl());
		} else {
			$this->category = $this->categoryManager->getActiveCategoryByUrl($catUrl);
			$this->editLink = $this->link(':Admin:Post:category', [$this->blog->getUrl(), $this->category->getId()]);
		}


		$this->checkBlog();
		$this->template->blog = $this->blog;
		$this->template->activeCategory = isset($this->category) ? $this->category : NULL;
		$this->template->categories = $this->categoryManager->getActiveBlogCategories($this->blog);
		$this->template->posts = isset($this->category) ? $this->postManager->getCategoryPostList($this->category) : $this->postManager->getPostList($this->blog);
	}

	/**
	 * @param $postUrl
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 * @throws \Exception
	 * @throws \Nette\Application\BadRequestException
	 */
	public function actionDetail($postUrl)
	{
		$this->post = $this->postManager->findByUrl($postUrl);

		if (empty($this->post) || !$this->post->isPublic())
			$this->error('Post not found');


		$this->editLink = $this->link(':Admin:Post:edit', [$this->post->getBlog()->getUrl(), $this->post->getId()]);
		$this->postManager->addStatistics($this->post);
		$this->template->images = !empty($this->post->getGallery()) ? $this->imageManager->getActiveGalleryImages($this->post->getGallery()) : NULL;
		$this->template->post = $this->post;
		$this->template->nextPosts = $this->postManager->getNextPosts($this->post, 2);
	}

	public function renderDetail($postUrl)
	{
		$this->initPostHead();
	}

	public function renderDefault($url)
	{
		$this->initBlogHead();
	}

	/**
	 * @throws \Nette\Application\BadRequestException
	 */
	private function checkBlog()
	{
		if (empty($this->blog)) {
			$this->error('Tento blog neexistuje');
		}
	}

	private function initPostHead()
	{
		$this->template->headTitle = $this->post->getName();
		$this->template->headDescription = $this->post->getDescription();
		$this->template->headImage = empty($this->post->getImage()) ? NULL : $this->post->getImage();
	}

	private function initBlogHead()
	{
		$this->template->headTitle = $this->blog->getName();
		$this->template->headDescription = $this->blog->getDescription();
		$this->template->headImage = empty($this->blog->getImage()) ? NULL : $this->blog->getImage();
	}
}