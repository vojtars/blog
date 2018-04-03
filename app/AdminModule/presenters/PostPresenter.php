<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace AdminModule\Presenters;

use AdminModule\Components\CategoryForm;
use AdminModule\Components\ICategoryFormFactory;
use AdminModule\Components\IPostFormFactory;
use AdminModule\Components\IPostGridFactory;
use AdminModule\Components\PostForm;
use AdminModule\Components\PostGrid;
use Nette\Http\FileUpload;
use Tracy\Debugger;
use Vojtars\Model\Blog;
use Vojtars\Model\Category;
use Vojtars\Model\CategoryManager;
use Vojtars\Model\Gallery;
use Vojtars\Model\Post;
use Vojtars\Model\PostRepository;

class PostPresenter extends BasePresenter
{
	/**
	 * @var IPostFormFactory
	 */
	private $postFormFactory;

	/**
	 * @var IPostGridFactory
	 */
	private $postGridFactory;

	/**
	 * @var PostRepository
	 */
	private $postRepository;

	/**
	 * @var Post|NULL
	 */
	private $post;
	/**
	 * @var ICategoryFormFactory
	 */
	private $categoryFormFactory;

	/**
	 * @var Category|NULL
	 */
	private $category;
	/**
	 * @var CategoryManager
	 */
	private $categoryManager;

	/**
	 * PostPresenter constructor.
	 * @param IPostFormFactory     $postFormFactory
	 * @param IPostGridFactory     $postGridFactory
	 * @param PostRepository       $postRepository
	 * @param ICategoryFormFactory $categoryFormFactory
	 * @param CategoryManager      $categoryManager
	 */
	public function __construct(IPostFormFactory $postFormFactory, IPostGridFactory $postGridFactory,
	                            PostRepository $postRepository, ICategoryFormFactory $categoryFormFactory,
	                            CategoryManager $categoryManager)
	{
		parent::__construct();
		$this->postFormFactory = $postFormFactory;
		$this->postGridFactory = $postGridFactory;
		$this->postRepository = $postRepository;
		$this->categoryFormFactory = $categoryFormFactory;
		$this->categoryManager = $categoryManager;
	}

	/**
	 * @param string $blogUrl
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function actionList(string $blogUrl)
	{
		$this->blog = $this->blogRepository->findByUrl($blogUrl);
		$this->template->blogEntity = $this->blog;
	}

	/**
	 * @param string $blogUrl
	 * @param int    $categoryId
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 * @throws \Vojtars\Model\CategoryException
	 */
	public function actionCategory(string $blogUrl, int $categoryId)
	{
		$this->blog = $this->blogRepository->findByUrl($blogUrl);
		$this->category = $this->categoryManager->getCategory($categoryId);
		$this->template->blogEntity = $this->blog;
	}

	/**
	 * @param string      $blogUrl
	 * @param int|NULL $postId
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function actionEdit(string $blogUrl, int $postId = NULL)
	{
		$this->blog = $this->blogRepository->findByUrl($blogUrl);
		$this->post = empty($postId) ? NULL : $this->postRepository->find($postId);
	}

	/**
	 * @return PostGrid
	 */
	public function createComponentPostGrid(): PostGrid
	{
		$control = $this->postGridFactory->create();
		$control->setBlog($this->blog);
		$control->setUserEntity($this->userEntity);
		return $control;
	}

	/**
	 * @return PostForm
	 */
	public function createComponentPostForm(): PostForm
	{
		$control = $this->postFormFactory->create();
		$control->setBlog($this->blog);
		$control->setPost($this->post);
		$control->setUserEntity($this->userEntity);
		return $control;
	}

	/**
	 * @return CategoryForm
	 */
	public function createComponentCategoryForm(): CategoryForm
	{
		$control = $this->categoryFormFactory->create();
		$control->setUserEntity($this->userEntity);
		$control->setBlog($this->blog);
		$control->setCategory($this->category);
		return $control;
	}

}
