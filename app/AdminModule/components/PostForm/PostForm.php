<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace AdminModule\Components;

use Doctrine\ORM\Query\AST\NullComparisonExpression;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Http\FileUpload;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use Nette\Utils\Random;
use Nette\Utils\Strings;
use Tracy\Debugger;
use Tracy\ILogger;
use Vojtars\Model\Blog;
use Vojtars\Model\Category;
use Vojtars\Model\CategoryRepository;
use Vojtars\Model\File;
use Vojtars\Model\FileManager;
use Vojtars\Model\Gallery;
use Vojtars\Model\GalleryManager;
use Vojtars\Model\GalleryRepository;
use Vojtars\Model\Image;
use Vojtars\Model\ImageManager;
use Vojtars\Model\NoPostException;
use Vojtars\Model\Post;
use Vojtars\Model\PostHasFile;
use Vojtars\Model\PostManager;
use Vojtars\Model\User;

class PostForm extends Control
{
	use OwnTemplate;

	/**
	 * @var array
	 */
	public $onChange = [];

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var Blog|NULL
	 */
	private $blog;

	/**
	 * @var GalleryRepository
	 */
	private $galleryRepository;

	/**
	 * @var ImageManager
	 */
	private $imageManager;

	/**
	 * @var User
	 */
	private $userEntity;

	/**
	 * @var Post|null
	 */
	private $post;

	/**
	 * @var CategoryRepository
	 */
	private $categoryRepository;
	/**
	 * @var PostManager
	 */
	private $postManager;
	/**
	 * @var GalleryManager
	 */
	private $galleryManager;
	/**
	 * @var FileManager
	 */
	private $fileManager;

	/**
	 * UserGrid constructor.
	 * @param EntityManager      $entityManager
	 * @param PostManager        $postManager
	 * @param GalleryRepository  $galleryRepository
	 * @param ImageManager       $imageManager
	 * @param CategoryRepository $categoryRepository
	 * @param GalleryManager     $galleryManager
	 * @param FileManager        $fileManager
	 */
	public function __construct(EntityManager $entityManager, PostManager $postManager, GalleryRepository $galleryRepository,
	                            ImageManager $imageManager, CategoryRepository $categoryRepository, GalleryManager $galleryManager,
	                            FileManager $fileManager)
	{
		parent::__construct();
		$this->entityManager = $entityManager;
		$this->galleryRepository = $galleryRepository;
		$this->imageManager = $imageManager;
		$this->categoryRepository = $categoryRepository;
		$this->postManager = $postManager;
		$this->galleryManager = $galleryManager;
		$this->fileManager = $fileManager;
		$this->setTemplateName('postForm.latte');

	}

	/**
	 * @param \Vojtars\Model\Blog|NULL $blog
	 */
	public function setBlog(Blog $blog = NULL)
	{
		$this->blog = $blog;
	}

	/**
	 * @param \Vojtars\Model\User $userEntity
	 */
	public function setUserEntity(User $userEntity)
	{
		$this->userEntity = $userEntity;
	}

	/**
	 * @param \Vojtars\Model\Post|NULL $post
	 */
	public function setPost(Post $post = NULL)
	{
		$this->post = $post;
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
	protected function createComponentPostForm()
	{
		$galleries = $this->galleryManager->getGalleriesForSelect();
		$files = $this->fileManager->getFilesForSelect();
		$form = new Form();
		$form->addHidden('id', empty($this->post) ? NULL : $this->post->getId());
		$form->addText('name', 'Název:');
		$form->addText('publicDate', 'Datum publikace');
		$form->addTextArea('perex', 'Perex');
		$form->addTextArea('content', 'Text');
		$form->addCheckbox('public', 'publikovat');
		$form->addSelect('category', 'Kategorie', $this->categoryRepository->getCategoriesForSelect($this->blog))
			->setRequired('Musíte vybrat kategorii');
		$form->addSelect('gallery', 'Galerie', $galleries);
		$form->addMultiSelect('files', 'Soubory:', $files);
		$form->addUpload('image', 'Úvodní fotka');
		$form->addSubmit('create', 'Vytvořit');

		if (!empty($this->post)) {
			$form->setDefaults([
				'name'          => $this->post->getName(),
				'perex'         => $this->post->getDescription(),
				'gallery'       => empty($this->post->getGallery()) ? 0 : $this->post->getGallery()->getId(),
				'content'       => $this->post->getContent(),
				'category'      => $this->post->getCategory()->getId(),
				'files'         => $this->fileManager->getPostFilesIds($this->post),
				'publicDate'    => $this->post->getPublicDate()->format('Y-m-d H:i'),
				'public'        => $this->post->isPublic(),
			]);
		} else {
			$form->setDefaults([
				'publicDate' => (new \DateTime())->format('Y-m-d H:i')
			]);
		}

		$form->onSuccess[] = [$this, 'postFormSucceeded'];
		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 * @param                            $values
	 * @throws \Nette\Application\AbortException
	 */
	public function postFormSucceeded(Form $form, $values)
	{
		if (empty($values->id)) {
			$this->addNewPost($values);
		} else {
			$this->editActualPost($values);
		}
	}

	/**
	 * @param \Nette\Utils\ArrayHash $values
	 * @throws \Nette\Application\AbortException
	 */
	private function addNewPost(ArrayHash $values)
	{
		$badImageMessage = FALSE;
		try {
			/** @var Category $category */
			$category = $values->category == 0 ? NULL : $this->categoryRepository->find((int)$values->category);

			/** @var Gallery $gallery */
			$gallery = $values->gallery == 0 ? NULL : $this->galleryManager->getGallery((int)$values->gallery);

			$newPost = new Post($category, $values->name, $this->userEntity, $this->blog);
			$newPost->setDescription($values->perex);
			$newPost->setContent($values->content);
			$newPost->setDateEdit(new DateTime());
			$newPost->setLastEditUser($this->userEntity);
			$newPost->setPublicDate(new DateTime($values->publicDate));
			$newPost->setUrl($this->checkPostUrl(Strings::webalize($values->name)));
			$newPost->setPublic((bool)$values->public);
			$newPost->setGallery($gallery);

			if (!empty($values->image) && (($values->image instanceof FileUpload) && (!empty($values->image->name)))) {
				/** @var Gallery $gallery */
				$gallery = $this->galleryRepository->getBlogGallery($this->blog);
				/** @var Image $newImage */
				$newImage = $this->imageManager->saveImage($values->image, $this->userEntity, $gallery, $values->name);
				$newPost->setImage($newImage);
				if (empty($newImage)) {
					$badImageMessage = TRUE;
				}
			}

			$this->entityManager->persist($newPost);
			$this->entityManager->flush($newPost);

			$this->saveFiles($values->files, $newPost);

		} catch (\Exception $exception) {
			Debugger::log($exception, ILogger::ERROR);
			$this->getPresenter()->flashMessage('Nedařilo se přidat Článek', 'danger');
			$this->getPresenter()->redirect('this');
		}

		if ($badImageMessage) {
			$this->getPresenter()->flashMessage('Článek přidán, ale obrázek se nepodařilo nahrát');
			$this->getPresenter()->redirect('this');
		} else {
			$this->getPresenter()->flashMessage('Článek přidán');
			$this->getPresenter()->redirect('Post:list', $this->blog->getUrl());

		}
	}



	/**
	 * @param \Nette\Utils\ArrayHash $values
	 * @throws \Nette\Application\AbortException
	 */
	private function editActualPost(ArrayHash $values)
	{
		$badImageMessage = FALSE;
		try {
			/** @var Category $category */
			$category = $values->category == 0 ? NULL : $this->categoryRepository->find($values->category);

			/** @var Gallery $gallery */
			$gallery = $values->gallery == 0 ? NULL : $this->galleryManager->getGallery((int)$values->gallery);

			$post = $this->postManager->getPost((int)$values->id);
			$post->setCategory($category);
			$post->setDescription($values->perex);
			$post->setContent($values->content);
			$post->setDateEdit(new DateTime());
			$post->setLastEditUser($this->userEntity);
			$post->setPublicDate(new DateTime($values->publicDate));
			$post->setPublic((bool)$values->public);

			if ($values->name != $post->getName())
				$post->setUrl($this->checkPostUrl(Strings::webalize($values->name)));

			$post->setName($values->name);

			if (!empty($gallery))
				$post->setGallery($gallery);

			if (!empty($values->image) && (($values->image instanceof FileUpload) && (!empty($values->image->name)))) {
				/** @var Gallery $gallery */
				$gallery = $this->galleryRepository->getBlogGallery($this->blog);
				/** @var Image $newImage */
				$newImage = $this->imageManager->saveImage($values->image, $this->userEntity, $gallery, $values->name);
				$post->setImage($newImage);
				if (empty($newImage)) {
					$badImageMessage = TRUE;
				}
			}

			$this->saveFiles($values->files, $post);
			$this->entityManager->flush($post);
		} catch (\Exception $exception) {
			Debugger::log($exception, ILogger::ERROR);
			$this->getPresenter()->flashMessage('Nedařilo se uložit Článek', 'danger');
			$this->getPresenter()->redirect('this');
		}

		if ($badImageMessage) {
			$this->getPresenter()->flashMessage('Článek uložen, ale obrázek se nepodařilo nahrát');
			$this->getPresenter()->redirect('this');
		} else {
			$this->getPresenter()->flashMessage('Článek uložen');
			$this->getPresenter()->redirect('Post:list', $this->blog->getUrl());

		}
	}

	/**
	 * @param string $url
	 * @return string
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	private function checkPostUrl(string $url)
	{
		try {
			$this->postManager->getPostByUrl($url);
			return $this->checkPostUrl($url.'-'.Random::generate(1, 'a-z'));
		} catch (NoPostException $noUserException) {
			return $url;
		}
	}

	/**
	 * @param array $files
	 * @param Post  $post
	 * @throws \Exception
	 */
	private function saveFiles(array $files, Post $post)
	{
		$this->fileManager->removeAllPostFiles($post);
		foreach ($files as $fileId) {
			$file = $this->fileManager->getFile($fileId);
			$newFile = new PostHasFile($post, $file);
			$this->entityManager->persist($newFile);
			$this->entityManager->flush($newFile);
		}
	}
}