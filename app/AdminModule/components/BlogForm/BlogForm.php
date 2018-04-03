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
use Vojtars\Model\BlogRepository;
use Vojtars\Model\Gallery;
use Vojtars\Model\GalleryRepository;
use Vojtars\Model\Image;
use Vojtars\Model\ImageManager;
use Vojtars\Model\NoBlogException;
use Vojtars\Model\User;

class BlogForm extends Control
{
	use OwnTemplate;

	/**
	 * @var EntityManager
	 */
	private $entityManager;
	
	/**
	 * @var BlogRepository
	 */
	private $blogRepository;
	
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
	 * @var Blog|NULL
	 */
	private $blog = NULL;


	/**
	 * BlogForm constructor.
	 * @param EntityManager     $entityManager
	 * @param BlogRepository    $blogRepository
	 * @param GalleryRepository $galleryRepository
	 * @param ImageManager      $imageManager
	 */
	public function __construct(EntityManager $entityManager, BlogRepository $blogRepository, GalleryRepository $galleryRepository, ImageManager $imageManager)
	{
		parent::__construct();
		$this->entityManager = $entityManager;
		$this->setTemplateName('blogForm.latte');
		$this->blogRepository = $blogRepository;
		$this->imageManager = $imageManager;
		$this->galleryRepository = $galleryRepository;
	}

	public function setBlog(Blog $blog = NULL)
	{
		$this->blog = $blog;
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
	 * @param $userEntity
	 */
	public function setUserEntity($userEntity)
	{
		$this->userEntity = $userEntity;
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentBlogForm()
	{
		$form = new Form();
		$form->addHidden('id', empty($this->blog) ? NULL : $this->blog->getId());
		$form->addText('name', 'Jméno:');
		$form->addText('url', 'URL:');
		$form->addTextArea('description', 'Popis');
		$form->addUpload('image', 'Úvodní fotka');
		$form->addSubmit('create', 'Vytvořit');

		if (!empty($this->blog)) {
			$form->setDefaults([
				'name'          => $this->blog->getName(),
				'url'           => $this->blog->getUrl(),
				'description'   => $this->blog->getDescription(),
			]);
		}
		$form->onSuccess[] = [$this, 'blogFormSucceeded'];
		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 * @param                            $values
	 * @throws \Nette\Application\AbortException
	 */
	public function blogFormSucceeded(Form $form, $values)
	{
		if (empty($values->id)) {
			$this->addNewBlog($values);
		} else {
			$this->editActualBlog($values);
		}
	}

	/**
	 * @param ArrayHash $values
	 * @throws \Nette\Application\AbortException
	 */
	private function addNewBlog(ArrayHash $values)
	{
		try {
			$badImageMessage = FALSE;

			if (!empty($values->image) && (($values->image instanceof FileUpload) && (!empty($values->image->name)))) {
				/** @var Gallery $gallery */
				$gallery = $this->galleryRepository->find(Gallery::DEFAULT_GALLERY);
				/** @var Image $image */
				$image = $this->imageManager->saveImage($values->image, $this->userEntity, $gallery, $values->name);
				if (empty($image)) {
					$badImageMessage = TRUE;
				}
			} else {
				$image = NULL;
			}

			$blog = new Blog($values->name, $this->checkBlogtUrl($values->url), $this->userEntity);

			if (!empty($image))
				$blog->setImage($image);

			$blog->setDescription($values->description);
			$this->entityManager->persist($blog);
			$this->entityManager->flush($blog);

			$gallery = new Gallery('Blog - ' . $blog->getName());
			$gallery->setBlog($blog);
			$gallery->setDescription('Galerie obrázků použitých v blogu : ' . $blog->getName());
			$this->entityManager->persist($gallery);
			$this->entityManager->flush($gallery);
		} catch (\Exception $exception) {
			Debugger::log($exception);
			$this->getPresenter()->flashMessage('Nedařilo se přidat Blog', 'danger');
			$this->getPresenter()->redirect('this');
		}

		if ($badImageMessage) {
			$this->getPresenter()->flashMessage('Blog přidán, ale obrázek se nepodařilo nahrát');
			$this->getPresenter()->redirect('Blog:');
		} else {
			$this->getPresenter()->flashMessage('Blog přidán');
			$this->getPresenter()->redirect('Blog:');
		}
	}

	/**
	 * @param ArrayHash $values
	 * @throws \Nette\Application\AbortException
	 */
	private function editActualBlog(ArrayHash $values)
	{
		try {
			$badImageMessage = FALSE;

			$blog = $this->blogRepository->getBlog((int)$values->id);
			if (!empty($values->image) && (($values->image instanceof FileUpload) && (!empty($values->image->name)))) {
				/** @var Gallery $gallery */
				$gallery = $this->galleryRepository->find(Gallery::DEFAULT_GALLERY);
				/** @var Image $image */
				$image = $this->imageManager->saveImage($values->image, $this->userEntity, $gallery, $values->name);
				if (empty($image)) {
					$badImageMessage = TRUE;
				}
			} else {
				$image = NULL;
			}

			if (!empty($image))
				$blog->setImage($image);

			if ($values->url != $blog->getUrl())
				$blog->setUrl($this->checkBlogtUrl($values->url));

			$blog->setName($values->name);
			$blog->setDescription($values->description);
			$this->entityManager->flush($blog);
		} catch (\Exception $exception) {
			Debugger::log($exception);
			$this->getPresenter()->flashMessage('Nedařilo se upravit Blog', 'danger');
			$this->getPresenter()->redirect('this');
		}

		if ($badImageMessage) {
			$this->getPresenter()->flashMessage('Blog upraven, ale obrázek se nepodařilo nahrát');
			$this->getPresenter()->redirect('Blog:');
		} else {
			$this->getPresenter()->flashMessage('Blog upraven');
			$this->getPresenter()->redirect('Blog:');
		}
	}

	/**
	 * @param string $url
	 * @return string
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 * @throws \Vojtars\Model\NoBlogException
	 */
	private function checkBlogtUrl(string $url)
	{
		try {
			$this->blogRepository->getBlogByUrl($url);
			return $this->checkBlogtUrl($url.'-'.Random::generate(1, 'a-z'));
		} catch (NoBlogException $noBlogException) {
			return $url;
		}
	}

}