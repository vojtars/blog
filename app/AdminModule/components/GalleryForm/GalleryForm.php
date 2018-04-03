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
use Vojtars\Model\ImageRepository;
use Vojtars\Model\User;

class GalleryForm extends Control
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
	 * @var Gallery
	 */
	private $gallery;

	/**
	 * @var ImageManager
	 */
	private $imageManager;

	/**
	 * @var User
	 */
	private $userEntity;

	/**
	 * @var ImageRepository
	 */
	private $imageRepository;


	/**
	 * GalleryForm constructor.
	 * @param EntityManager     $entityManager
	 * @param GalleryRepository $galleryRepository
	 * @param ImageManager      $imageManager
	 * @param ImageRepository   $imageRepository
	 */
	public function __construct(EntityManager $entityManager, GalleryRepository $galleryRepository,
	                            ImageManager $imageManager, ImageRepository $imageRepository)
	{
		parent::__construct();
		$this->entityManager = $entityManager;
		$this->setTemplateName('galleryForm.latte');
		$this->galleryRepository = $galleryRepository;
		$this->imageManager = $imageManager;
		$this->imageRepository = $imageRepository;
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
	 * @param \Vojtars\Model\Gallery $gallery
	 */
	public function setGallery(Gallery $gallery)
	{
		$this->gallery = $gallery;
	}

	/**
	 * @param \Vojtars\Model\User $user
	 */
	public function setUserEntity(User $user)
	{
		$this->userEntity = $user;
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentUploadForm()
	{
		$form = new Form();
		$form->addMultiUpload('images', 'Obázky');
		$form->addSubmit('upload', 'Vytvořit');
		$form->onSuccess[] = [$this, 'uploadFormSucceeded'];
		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 * @param                            $values
	 * @throws \Nette\Application\AbortException
	 */
	public function uploadFormSucceeded(Form $form, $values)
	{
		try {
			$badImageMessage = FALSE;

			if (!empty($values->images)) {
				$i = 1;
				/** @var FileUpload $image */
				foreach ($values->images as $image) {
					/** @var Image $newImage */
					$newImage = $this->imageManager->saveImage($image, $this->userEntity, $this->gallery, 'Obrázek ' . $i);
					if (empty($image)) {
						$badImageMessage = TRUE;
					}
				}
			}
		} catch (\Exception $exception) {
			$this->getPresenter()->flashMessage('Nedařilo se přidat Blog', 'danger');
			$this->getPresenter()->redirect('this');
		}

		if ($badImageMessage) {
			$this->getPresenter()->flashMessage('Nebyly nahrány všechny obrázky', 'danger');
			$this->getPresenter()->redirect('this');
		} else {
			$this->getPresenter()->flashMessage('Obrázky jsou nahrány');
			$this->getPresenter()->redirect('this');
		}

	}

	/**
	 * @return DataGrid
	 * @throws \Ublaboo\DataGrid\Exception\DataGridColumnStatusException
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
	 */
	public function createComponentImageGrid()
	{
		$grid = new DataGrid();

		$grid->setDataSource($this->imageRepository->getImagesForGrid($this->gallery));
		$grid->setItemsPerPageList([20, 50, 100, 200]);
		$grid->setRememberState(FALSE);

		$grid->addColumnText('id', 'Id')
			->setSortable();

		$grid->addColumnText('name', 'Obrázek')
			->setSortable()
			->setRenderer(function (Image $image) {
				return Html::el('img')
					->setAttribute('style', 'max-width: 128px; max-height: 128px;')
					->setAttribute('src', '/'.$image->getMiniNameWithPath())
					->setAttribute('alt', $image->getDescription());
			});

		$grid->addColumnText('url', 'URL')
			->setRenderer(function (Image $image) {
				return $image->getUrl();
			});

		$grid->addColumnText('description', 'Popis obrázku')
			->setSortable()
			->setEditableCallback(function ($id, $value) {
				/** @var Image $image */
				$image = $this->imageRepository->find((int)$id);
				$image->setDescription($value);
				$this->entityManager->flush($image);
			});

		$grid->addColumnText('dateAdd', 'Vytvořeno')
			->setSortable()
			->setRenderer(function (Image $image) {
				if (empty($image->getDateAdd())) {
					return '---';
				} else {
					return $image->getDateAdd()->format('d.m.Y H:i:s');
				}
			});

		if ($this->gallery->getId() == Gallery::DEFAULT_GALLERY || !empty($this->gallery->getBlog())) {
		} else {
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

				/** @var Image $image */
				$image = $this->imageRepository->find((int)$id);
				$image->setActive((bool)$newValue);
				$this->entityManager->flush($image);

				if ($this->getPresenter()->isAjax()) {
					$this['imageGrid']->reload();
				} else {
					$this->redirect('this');
				}
			};
		}

		$grid->addAction('delete', 'Smazat', 'delete!')
			->setIcon('trash')
			->setTitle('Smazat')
			->setClass('btn btn-xs btn-danger ajax');

		return $grid;
	}

	/**
	 * @param int $id
	 * @throws \Exception
	 * @throws \Nette\Application\AbortException
	 */
	public function handleDelete(int $id)
	{
		$error = FALSE;
		try {
			/** @var Image $image */
			$image = $this->imageRepository->find($id);
			unlink(IMG_GALLERY_DIR . $image->getGallery()->getId() . '/' . $image->getName());
			unlink(IMG_GALLERY_DIR . $image->getGallery()->getId() . '/' . $image->getMiniName());
			$this->entityManager->remove($image);
			$this->entityManager->flush($image);
		} catch (\Exception $exception) {
			$error = TRUE;
		}

		if ($error) {
			$this->getPresenter()->flashMessage('Obrázek se nepodařilo smazat.', 'danger');
		} else {
			$this->getPresenter()->flashMessage('Smazáno');
		}

		if ($this->getPresenter()->isAjax()) {
			$this->getPresenter()->redrawControl('flashes');
			$this['imageGrid']->reload();
		} else {
			$this->redirect('this');
		}
	}

}