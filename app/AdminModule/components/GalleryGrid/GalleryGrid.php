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
use Nette\Utils\Html;
use Tracy\Debugger;
use Ublaboo\DataGrid\DataGrid;
use Vojtars\Model\Gallery;
use Vojtars\Model\GalleryRepository;
use Vojtars\Model\Image;
use Vojtars\Model\ImageManager;
use Vojtars\Model\Post;
use Vojtars\Model\User;

class GalleryGrid extends Control
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
	private $user;


	/**
	 * UserGrid constructor.
	 * @param EntityManager     $entityManager
	 * @param GalleryRepository $galleryRepository
	 * @param ImageManager      $imageManager
	 */
	public function __construct(EntityManager $entityManager, GalleryRepository $galleryRepository, ImageManager $imageManager)
	{
		parent::__construct();
		$this->entityManager = $entityManager;
		$this->setTemplateName('galleryGrid.latte');
		$this->galleryRepository = $galleryRepository;
		$this->imageManager = $imageManager;
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
	 * @param \Vojtars\Model\User $user
	 */
	public function setUserEntity(User $user)
	{
		$this->user = $user;
	}

	/**
	 * @return DataGrid
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
	 */
	public function createComponentGalleryGrid()
	{
		$grid = new DataGrid();

		$grid->setDataSource($this->galleryRepository->getDataGridQuery());
		$grid->setItemsPerPageList([20, 50, 100, 200]);
		$grid->setRememberState(FALSE);

		$grid->addColumnText('id', 'Id')
			->setSortable();

		$grid->addColumnText('image', 'Náhled')
			->setSortable()
			->setRenderer(function (Gallery $gallery) {
				if (empty($gallery->getImage())) {
					return NULL;
				} else {
					return Html::el('img')
						->setAttribute('style', 'max-width: 128px; max-height: 128px;')
						->setAttribute('src', '/' . $gallery->getImage()->getMiniNameWithPath())
						->setAttribute('alt', $gallery->getImage()->getDescription());
				}

			});

		$grid->addColumnText('name', 'Název')
			->setSortable()
			->setEditableCallback(function ($id, $value) {
				/** @var Gallery $gallery */
				$gallery = $this->galleryRepository->find((int)$id);
				$gallery->setName($value);
				$this->entityManager->flush($gallery);
			});

		$grid->addColumnText('description', 'Popis')
			->setSortable()
			->setEditableCallback(function ($id, $value) {
				/** @var Gallery $gallery */
				$gallery = $this->galleryRepository->find((int)$id);
				$gallery->setDescription($value);
				$this->entityManager->flush($gallery);
			});

		$grid->addColumnText('dateAdd', 'Vytvořeno')
			->setSortable()
			->setRenderer(function (Gallery $gallery) {
				if (empty($gallery->getDateAdd())) {
					return '---';
				} else {
					return $gallery->getDateAdd()->format('d.m.Y H:i:s');
				}
			});

		$grid->addAction('edit', 'Upravit', 'edit!')
			->setIcon('edit')
			->setTitle('Upravit')
			->setClass('btn btn-xs btn-success');

		return $grid;
	}

	/**
	 * @param int $id
	 * @throws \Nette\Application\AbortException
	 */
	public function handleEdit(int $id)
	{
		$this->getPresenter()->redirect('Gallery:detail', $id);
	}

	/**
	 * @return Form
	 */
	protected function createComponentGalleryForm()
	{
		$form = new Form();
		$form->addText('name', 'Jméno:');
		$form->addTextArea('description', 'Popis');
		$form->addUpload('image', 'Úvodní fotka');
		$form->addSubmit('create', 'Vytvořit');
		$form->onSuccess[] = [$this, 'galleryFormSucceeded'];
		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 * @param                            $values
	 * @throws \Nette\Application\AbortException
	 */
	public function galleryFormSucceeded(Form $form, $values)
	{
		try {
			$gallery = new Gallery($values->name);
			if (!empty($values->description)) {
				$gallery->setDescription($values->description);
			}

			$this->entityManager->persist($gallery);
			$this->entityManager->flush($gallery);
			$badImageMessage = FALSE;

			if (!empty($values->image) && (($values->image instanceof FileUpload) && (!empty($values->image->name)))) {
				/** @var Image $image */
				$image = $this->imageManager->saveImage($values->image, $this->user, $gallery, $values->name);
				if (empty($image)) {
					$badImageMessage = TRUE;
				} else {
					$gallery->setImage($image);
					$this->entityManager->flush($gallery);
				}
			}
		} catch (\Exception $exception) {
			$this->getPresenter()->flashMessage('Nedařilo se přidat Blog', 'danger');
			$this->getPresenter()->redirect('this');
		}

		if ($badImageMessage) {
			$this->getPresenter()->flashMessage('Blog přidán, ale obrázek se nepodařilo nahrát');
			$this->getPresenter()->redirect('Gallery:');
		} else {
			$this->getPresenter()->flashMessage('Blog přidán');
			$this->getPresenter()->redirect('Gallery:');
		}

	}

}