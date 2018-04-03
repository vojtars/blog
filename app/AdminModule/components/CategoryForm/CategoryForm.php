<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace AdminModule\Components;

use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Http\FileUpload;
use Nette\Utils\ArrayHash;
use Nette\Utils\Random;
use Tracy\Debugger;
use Vojtars\Model\Blog;
use Vojtars\Model\BlogManager;
use Vojtars\Model\Category;
use Vojtars\Model\CategoryException;
use Vojtars\Model\CategoryManager;
use Vojtars\Model\Gallery;
use Vojtars\Model\GalleryRepository;
use Vojtars\Model\Image;
use Vojtars\Model\ImageManager;
use Vojtars\Model\User;

class CategoryForm extends Control
{
	use OwnTemplate;

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var ImageManager
	 */
	private $imageManager;

	/**
	 * @var User
	 */
	private $userEntity;

	/**
	 * @var GalleryRepository
	 */
	private $galleryRepository;

	/**
	 * @var Blog
	 */
	private $blog;

	/**
	 * @var Category|NULL
	 */
	private $category = NULL;
	/**
	 * @var CategoryManager
	 */
	private $categoryManager;
	/**
	 * @var BlogManager
	 */
	private $blogManager;


	/**
	 * CategoryForm constructor.
	 * @param EntityManager     $entityManager
	 * @param GalleryRepository $galleryRepository
	 * @param BlogManager       $blogManager
	 * @param ImageManager      $imageManager
	 * @param CategoryManager   $categoryManager
	 */
	public function __construct(EntityManager $entityManager, GalleryRepository $galleryRepository, BlogManager $blogManager,
	                            ImageManager $imageManager, CategoryManager $categoryManager)
	{
		parent::__construct();
		$this->entityManager = $entityManager;
		$this->setTemplateName('categoryForm.latte');
		$this->imageManager = $imageManager;
		$this->galleryRepository = $galleryRepository;
		$this->categoryManager = $categoryManager;
		$this->blogManager = $blogManager;
	}

	/**
	 * @param Blog $blog
	 */
	public function setBlog(Blog $blog)
	{
		$this->blog = $blog;
	}

	/**
	 * @param Category|NULL $category
	 */
	public function setCategory(Category $category = NULL)
	{
		$this->category = $category;
	}

	/**
	 * @param $userEntity
	 */
	public function setUserEntity($userEntity)
	{
		$this->userEntity = $userEntity;
	}

	/**
	 * @throws \ReflectionException
	 */
	public function render()
	{
		$template = $this->getTemplate();
		$template->setFile($this->getTemplateFullPath());
		$template->render();
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentCategoryForm()
	{
		$form = new Form();
		$form->addHidden('id', empty($this->category) ? NULL : $this->category->getId());
		$form->addHidden('blogId', $this->blog->getId());
		$form->addText('name', 'Jméno:');
		$form->addText('url', 'URL:');
		$form->addTextArea('description', 'Popis');
		$form->addUpload('image', 'Úvodní fotka');
		$form->addSubmit('create', 'Vytvořit');

		if (!empty($this->blog)) {
			$form->setDefaults([
				'name'          => $this->category->getName(),
				'url'           => $this->category->getUrl(),
				'description'   => $this->category->getDescription(),
			]);
		}
		$form->onSuccess[] = [$this, 'categoryFormSucceeded'];
		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 * @param                            $values
	 * @throws \Nette\Application\AbortException
	 */
	public function categoryFormSucceeded(Form $form, $values)
	{
		if (empty($values->id)) {
			$this->addNewCategory($values);
		} else {
			$this->editActualCategory($values);
		}
	}

	/**
	 * @param ArrayHash $values
	 * @throws \Nette\Application\AbortException
	 */
	private function addNewCategory(ArrayHash $values)
	{
		try {
			$badImageMessage = FALSE;

			$blog = $this->blogManager->getBlog((int)$values->blogId);
			if (!empty($values->image) && (($values->image instanceof FileUpload) && (!empty($values->image->name)))) {
				/** @var Gallery $gallery */
				$gallery = $this->galleryRepository->getBlogGallery($blog);
				/** @var Image $image */
				$image = $this->imageManager->saveImage($values->image, $this->userEntity, $gallery, $values->name);
				if (empty($image)) {
					$badImageMessage = TRUE;
				}
			} else {
				$image = NULL;
			}

			$category = new Category($values->name, $blog, $this->checkCategoryUrl($values->url));

			if (!empty($image))
				$category->setImage($image);

			$category->setDescription($values->description);
			$this->entityManager->persist($category);
			$this->entityManager->flush($category);
		} catch (\Exception $exception) {
			Debugger::log($exception);
			$this->getPresenter()->flashMessage('Nedařilo se přidat kategorii', 'danger');
			$this->getPresenter()->redirect('this');
		}

		if ($badImageMessage) {
			$this->getPresenter()->flashMessage('Kategorie přidána, ale obrázek se nepodařilo nahrát');
			$this->getPresenter()->redirect('Post:list', $blog->getUrl());
		} else {
			$this->getPresenter()->flashMessage('Kategorie přidána');
			$this->getPresenter()->redirect('Post:list', $blog->getUrl());
		}
	}

	/**
	 * @param ArrayHash $values
	 * @throws \Nette\Application\AbortException
	 */
	private function editActualCategory(ArrayHash $values)
	{
		try {
			$badImageMessage = FALSE;

			$blog = $this->blogManager->getBlog((int)$values->blogId);
			$category = $this->categoryManager->getCategory((int)$values->id);
			if (!empty($values->image) && (($values->image instanceof FileUpload) && (!empty($values->image->name)))) {
				/** @var Gallery $gallery */
				$gallery = $this->galleryRepository->getBlogGallery($blog);
				/** @var Image $image */
				$image = $this->imageManager->saveImage($values->image, $this->userEntity, $gallery, $values->name);
				if (empty($image)) {
					$badImageMessage = TRUE;
				}
			} else {
				$image = NULL;
			}

			if (!empty($image))
				$category->setImage($image);

			if ($values->url != $category->getUrl())
				$category->setUrl($this->checkCategoryUrl($values->url));

			$category->setName($values->name);
			$category->setDescription($values->description);
			$this->entityManager->flush($category);
		} catch (\Exception $exception) {
			Debugger::log($exception);
			$this->getPresenter()->flashMessage('Nedařilo se upravit kategorii', 'danger');
			$this->getPresenter()->redirect('this');
		}

		if ($badImageMessage) {
			$this->getPresenter()->flashMessage('Kategorie upravena, ale obrázek se nepodařilo nahrát');
			$this->getPresenter()->redirect('Post:list', $blog->getUrl());
		} else {
			$this->getPresenter()->flashMessage('Kategorie upravena');
			$this->getPresenter()->redirect('Post:list', $blog->getUrl());
		}
	}

	/**
	 * @param string $url
	 * @return string
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	private function checkCategoryUrl(string $url)
	{
		try {
			$this->categoryManager->getCategoryByUrl($url);
			return $this->checkCategoryUrl($url.'-'.Random::generate(1, 'a-z'));
		} catch (CategoryException $categoryException) {
			return $url;
		}
	}

}