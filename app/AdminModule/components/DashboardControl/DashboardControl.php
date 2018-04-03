<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace AdminModule\Components;

use DateTime;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Application\UI\Multiplier;
use Nette\Http\FileUpload;
use Nette\Utils\Html;
use Tracy\Debugger;
use Tracy\ILogger;
use Ublaboo\DataGrid\DataGrid;
use Vojtars\Model\BlogManager;
use Vojtars\Model\Gallery;
use Vojtars\Model\GalleryRepository;
use Vojtars\Model\Image;
use Vojtars\Model\ImageManager;
use Vojtars\Model\Post;
use Vojtars\Model\PostManager;
use Vojtars\Model\Project;
use Vojtars\Model\ProjectManager;
use Vojtars\Model\SettingsRepository;
use Vojtars\Model\User;

class DashboardControl extends Control
{
	use OwnTemplate;

	/**
	 * @var EntityManager
	 */
	private $entityManager;
	/**
	 * @var SettingsRepository
	 */
	private $settingsRepository;
	/**
	 * @var ImageManager
	 */
	private $imageManager;
	/**
	 * @var GalleryRepository
	 */
	private $galleryRepository;
	/**
	 * @var User
	 */
	private $userEntity;
	/**
	 * @var BlogManager
	 */
	private $blogManager;
	/**
	 * @var PostManager
	 */
	private $postManager;

	/**
	 * SettingsControl constructor.
	 * @param EntityManager      $entityManager
	 * @param SettingsRepository $settingsRepository
	 * @param ImageManager       $imageManager
	 * @param GalleryRepository  $galleryRepository
	 * @param BlogManager        $blogManager
	 * @param PostManager        $postManager
	 */
	public function __construct(EntityManager $entityManager, SettingsRepository $settingsRepository,
	                            ImageManager $imageManager, GalleryRepository $galleryRepository,
	                            BlogManager $blogManager, PostManager $postManager )
	{
		parent::__construct();
		$this->entityManager = $entityManager;
		$this->setTemplateName('dashboardControl.latte');
		$this->settingsRepository = $settingsRepository;
		$this->imageManager = $imageManager;
		$this->galleryRepository = $galleryRepository;
		$this->blogManager = $blogManager;
		$this->postManager = $postManager;
	}

	public function setUser(User $userEntity)
	{
		$this->userEntity = $userEntity;
	}

	/**
	 * @throws \ReflectionException
	 */
	public function render()
	{
		$template = $this->getTemplate();
		$template->settings = $this->settingsRepository->getSettings();
		$template->blogs = $this->blogManager->getActiveBlogs();
		$template->setFile($this->getTemplateFullPath());
		$template->render();
	}


	/**
	 * @return Multiplier
	 */
	protected function createComponentBlogGrid() {
		return new Multiplier(function ($id) {
			$blog = $this->blogManager->getBlog((int)$id);
			$grid = new DataGrid();
			$grid->setDataSource($this->postManager->getDataGridQueryForDashboard($blog));
			$grid->setItemsPerPageList([10, 20, 50]);
			$grid->setRememberState(TRUE);
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
					$post = $this->postManager->getPost((int)$id);
					$post->setName($value);
					$this->entityManager->flush($post);
				});

			$grid->addColumnText('viewCount', 'Počet zobrazení')
				->setSortable();

			$grid->addColumnText('dateEdit', 'Poslední změna')
				->setSortable()
				->setRenderer(function (Post $post) {
					if (empty($post->getDateEdit())) {
						return '---';
					} else {
						return $post->getDateEdit()->format('d.m.Y H:i:s');
					}
				});

			$grid->addColumnText('publicDate', 'Publikován dne')
				->setSortable()
				->setRenderer(function (Post $post) {
					if (empty($post->getPublicDate())) {
						return '---';
					} else {
						return $post->getPublicDate()->format('d.m.Y H:i:s');
					}
				});

			$grid->addColumnStatus('public', 'Publikován')
				->setSortable()
				->addOption(1, 'Publikovat')
				->setIcon('check')
				->setClass('btn-success')
				->endOption()
				->addOption(0, 'Schovat')
				->setIcon('close')
				->setClass('btn-danger')
				->endOption()
				->onChange[] = function ($id, $newValue) {

				/** @var Post $post */
				$post = $this->postManager->getPost((int)$id);
				$post->setPublic((bool)$newValue);

				if ((bool)$newValue) {
					$post->setPublicDate(new DateTime());
				}

				$this->entityManager->flush($post);

				if ($this->getPresenter()->isAjax()) {
					$this->redrawControl();
				} else {
					$this->redirect('this');
				}
			};

			$grid->addAction('editPost', 'Upravit', 'editPost!')
				->setIcon('edit')
				->setTitle('Upravit')
				->setClass('btn btn-xs btn-success ajax');

			return $grid;
		});
	}

	/**
	 * @param int $id
	 * @throws \Nette\Application\AbortException
	 * @throws \Vojtars\Model\NoPostException
	 */
	public function handleEditPost(int $id)
	{
		$post = $this->postManager->getPost($id);
		$this->getPresenter()->redirect('Post:edit', $post->getBlog()->getUrl(), $post->getId());
	}

	/**
	 * @param int $blogId
	 * @throws \Exception
	 * @throws \Vojtars\Model\NoBlogException
	 */
	public function handleChangeActiveBlog(int $blogId)
	{
		$blog = $this->blogManager->getBlog($blogId);
		if ($blog->isActive())
			$blog->setActive(FALSE);
		else
			$blog->setActive(TRUE);

		$this->entityManager->flush($blog);
		$this->redrawControl('dashboard');
	}


}