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
use Nette\Utils\DateTime;
use Nette\Utils\Html;
use Tracy\Debugger;
use Tracy\ILogger;
use Ublaboo\DataGrid\DataGrid;
use Vojtars\Model\Blog;
use Vojtars\Model\Category;
use Vojtars\Model\CategoryRepository;
use Vojtars\Model\Gallery;
use Vojtars\Model\GalleryRepository;
use Vojtars\Model\Image;
use Vojtars\Model\ImageManager;
use Vojtars\Model\Post;
use Vojtars\Model\PostRepository;
use Vojtars\Model\User;

class PostGrid extends Control
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
	 * @var PostRepository
	 */
	private $postRepository;

	/**
	 * @var Blog
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
	 * @var CategoryRepository
	 */
	private $categoryRepository;

	/**
	 * UserGrid constructor.
	 * @param EntityManager      $entityManager
	 * @param PostRepository     $postRepository
	 * @param GalleryRepository  $galleryRepository
	 * @param ImageManager       $imageManager
	 * @param CategoryRepository $categoryRepository
	 */
	public function __construct(EntityManager $entityManager, PostRepository $postRepository,
	                            GalleryRepository $galleryRepository, ImageManager $imageManager, CategoryRepository $categoryRepository)
	{
		parent::__construct();
		$this->entityManager = $entityManager;
		$this->setTemplateName('postGrid.latte');
		$this->postRepository = $postRepository;
		$this->galleryRepository = $galleryRepository;
		$this->imageManager = $imageManager;
		$this->categoryRepository = $categoryRepository;
	}

	/**
	 * @param \Vojtars\Model\Blog $blog
	 */
	public function setBlog(Blog $blog)
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
	 * @throws \ReflectionException
	 */
	public function render()
	{

		$template = $this->getTemplate();
		$template->blog = $this->blog;
		$template->setFile($this->getTemplateFullPath());
		$template->render();
	}

	/**
	 * @return DataGrid
	 * @throws \Ublaboo\DataGrid\Exception\DataGridColumnStatusException
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
	 */
	public function createComponentCategoryGrid()
	{
		$grid = new DataGrid();

		$grid->setDataSource($this->categoryRepository->getDataGridQuery($this->blog));
		$grid->setItemsPerPageList([5]);
		$grid->setRememberState(FALSE);

		$grid->addColumnText('id', 'Id')
			->setSortable();

		$grid->addColumnText('image', 'Náhled')
			->setSortable()
			->setRenderer(function (Category $category) {
				if (empty($category->getImage())) {
					return NULL;
				} else {
					return Html::el('img')
						->setAttribute('style', 'max-width: 100px; max-height: 100px;')
						->setAttribute('src', '/' . $category->getImage()->getMiniNameWithPath())
						->setAttribute('alt', $category->getImage()->getDescription());
				}
			});

		$grid->addColumnText('name', 'Název')
			->setSortable()
			->setEditableCallback(function ($id, $value) {
				/** @var Category $category */
				$category = $this->categoryRepository->find((int)$id);
				$category->setName($value);
				$this->entityManager->flush($category);
			});

		$grid->addColumnStatus('active', 'Aktivní')
			->setSortable()
			->addOption(1, 'Aktivní')
			->setIcon('check')
			->setClass('btn-success')
			->endOption()
			->addOption(0, 'Neaktivní')
			->setIcon('close')
			->setClass('btn-danger')
			->endOption()
			->onChange[] = function ($id, $newValue) {

			/** @var Category $category */
			$category = $this->categoryRepository->find((int)$id);
			$category->setActive((bool)$newValue);
			$this->entityManager->flush($category);

			if ($this->getPresenter()->isAjax()) {
				$this['categoryGrid']->reload();
			} else {
				$this->redirect('this');
			}
		};

		$grid->addAction('edit', 'Upravit', 'editCategory!')
			->setIcon('edit')
			->setTitle('Upravit')
			->setClass('btn btn-xs btn-success');


		return $grid;
	}

	/**
	 * @param int $id
	 * @throws \Nette\Application\AbortException
	 */
	public function handleEditCategory(int $id)
	{
		$this->getPresenter()->redirect('Post:category', $this->blog->getUrl(), $id);
	}

	/**
	 * @return DataGrid
	 * @throws \Ublaboo\DataGrid\Exception\DataGridColumnStatusException
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
	 */
	public function createComponentPostGrid()
	{
		$grid = new DataGrid();

		$grid->setDataSource($this->postRepository->getDataGridQuery($this->blog));
		$grid->setItemsPerPageList([20, 50, 100, 200]);
		$grid->setRememberState(FALSE);

		$grid->addColumnText('id', 'Id')
			->setSortable();

		$grid->addColumnText('image', 'Náhled')
			->setSortable()
			->setRenderer(function (Post $post) {
				if (empty($post->getImage())) {
					return NULL;
				} else {
					return Html::el('img')
						->setAttribute('style', 'max-width: 100px; max-height: 100px;')
						->setAttribute('src', '/' . $post->getImage()->getMiniNameWithPath())
						->setAttribute('alt', $post->getImage()->getDescription());
				}
			});

		$grid->addColumnText('name', 'Název')
			->setSortable()
			->setEditableCallback(function ($id, $value) {
				/** @var Post $post */
				$post = $this->postRepository->find((int)$id);
				$post->setName($value);
				$this->entityManager->flush($post);
			});

		$grid->addColumnText('description', 'Perex')
			->setSortable()
			->setEditableCallback(function ($id, $value) {
				/** @var Post $post */
				$post = $this->postRepository->find((int)$id);
				$post->setDescription($value);
				$this->entityManager->flush($post);
			});

		$grid->addColumnText('dateAdd', 'Vytvořeno')
			->setSortable()
			->setRenderer(function (Post $post) {
				if (empty($post->getDateAdd())) {
					return '---';
				} else {
					return $post->getDateAdd()->format('d.m.Y H:i:s');
				}
			});

		$grid->addColumnStatus('public', 'Publikován')
			->setSortable()
			->addOption(1, 'Publikován')
			->setIcon('check')
			->setClass('btn-success')
			->endOption()
			->addOption(0, 'Schován')
			->setIcon('close')
			->setClass('btn-danger')
			->endOption()
			->onChange[] = function ($id, $newValue) {

			/** @var Post $post */
			$post = $this->postRepository->find((int)$id);
			$post->setPublic((bool)$newValue);

			if ((bool)$newValue) {
				$post->setPublicDate(new DateTime());
			}

			$this->entityManager->flush($post);

			if ($this->getPresenter()->isAjax()) {
				$this['postGrid']->reload();
			} else {
				$this->redirect('this');
			}
		};

		$grid->addAction('edit', 'Upravit', 'editPost!')
			->setIcon('edit')
			->setTitle('Upravit')
			->setClass('btn btn-xs btn-success');

		return $grid;
	}

	/**
	 * @param int $id
	 * @throws \Nette\Application\AbortException
	 */
	public function handleEditPost(int $id)
	{
		$this->getPresenter()->redirect('Post:edit', $this->blog->getUrl(), $id);
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentCategoryForm()
	{
		$form = new Form();
		$form->addText('name', 'Název:');
		$form->addText('url', 'URL:');
		$form->addTextArea('description', 'Popis');
		$form->addUpload('image', 'Úvodní fotka');
		$form->addSubmit('create', 'Vytvořit');
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
		try {
			$badImageMessage = FALSE;
			$url = empty($values->url) ? NULL : $values->url;
			$category = new Category($values->name, $this->blog, $url);

			if (!empty($values->description)) {
				$category->setDescription($values->description);
			}

			if (!empty($values->image) && (($values->image instanceof FileUpload) && (!empty($values->image->name)))) {
				/** @var Gallery $gallery */
				$gallery = $this->galleryRepository->getBlogGallery($this->blog);
				/** @var Image $image */
				$image = $this->imageManager->saveImage($values->image, $this->userEntity, $gallery, $values->name);
				$category->setImage($image);
				if (empty($image)) {
					$badImageMessage = TRUE;
				}
			}

			$this->entityManager->persist($category);
			$this->entityManager->flush($category);
		} catch (\Exception $exception) {
			Debugger::log($exception, ILogger::ERROR);
			$this->getPresenter()->flashMessage('Nedařilo se přidat Kategorii', 'danger');
			$this->getPresenter()->redirect('this');
		}

		if ($badImageMessage) {
			$this->getPresenter()->flashMessage('Kategorie přidána, ale obrázek se nepodařilo nahrát');
			$this->getPresenter()->redirect('this');
		} else {
			$this->getPresenter()->flashMessage('Kategorie přidána');
			$this->getPresenter()->redirect('this');
		}

	}

}