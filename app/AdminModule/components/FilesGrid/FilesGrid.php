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
use Nette\Utils\Random;
use Tracy\Debugger;
use Ublaboo\DataGrid\DataGrid;
use Vojtars\Model\File;
use Vojtars\Model\FileException;
use Vojtars\Model\FileManager;
use Vojtars\Model\Gallery;
use Vojtars\Model\GalleryRepository;
use Vojtars\Model\Image;
use Vojtars\Model\ImageManager;
use Vojtars\Model\ImageRepository;
use Vojtars\Model\User;

class FilesGrid extends Control
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
	 * @var User
	 */
	private $userEntity;
	/**
	 * @var FileManager
	 */
	private $fileManager;


	/**
	 * GalleryForm constructor.
	 * @param EntityManager $entityManager
	 * @param FileManager   $fileManager
	 */
	public function __construct(EntityManager $entityManager, FileManager $fileManager)
	{
		parent::__construct();
		$this->entityManager = $entityManager;
		$this->setTemplateName('filesGrid.latte');
		$this->fileManager = $fileManager;
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
		$this->userEntity = $user;
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentUploadForm()
	{
		$form = new Form();
		$form->addMultiUpload('files', 'Soubroy');
		$form->addSubmit('upload', 'Nahrát');
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

			if (!empty($values->files)) {
				$i = 1;
				/** @var FileUpload $file */
				foreach ($values->files as $file) {
					if ($file->isOk()) {
						$fileName = $this->checkName($file->getSanitizedName());
						$newFile  = new File($fileName, $file->getSize());
						$file->move(FILES_DIR . $fileName);
						$this->entityManager->persist($newFile);
						$this->entityManager->flush($newFile);
					} else {
						$badImageMessage = TRUE;
					}
				}
			}
		} catch (\Exception $exception) {
			$this->getPresenter()->flashMessage('Nepodařilo se nahrát soubory', 'danger');
			$this->getPresenter()->redirect('this');
		}

		if ($badImageMessage) {
			$this->getPresenter()->flashMessage('Nebyly nahrány všechny soubory', 'danger');
			$this->getPresenter()->redirect('this');
		} else {
			$this->getPresenter()->flashMessage('Soubory jsou nahrány');
			$this->getPresenter()->redirect('this');
		}

	}

	/**
	 * @return DataGrid
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
	 */
	public function createComponentFilesGrid()
	{
		$grid = new DataGrid();

		$grid->setDataSource($this->fileManager->getFilesForGrid());
		$grid->setItemsPerPageList([20, 50, 100, 200]);
		$grid->setRememberState(FALSE);

		$grid->addColumnText('id', 'Id')
			->setSortable();

		$grid->addColumnText('name', 'Název obrázku')
			->setSortable()
			->setEditableCallback(function ($id, $value) {
				/** @var File $file */
				$file = $this->fileManager->getFile((int)$id);
				$file->setName($value);
				$this->entityManager->flush($file);
			});

		$grid->addColumnText('url', 'URL')
			->setRenderer(function (File $file) {
				return $file->getUrl();
			});

		$grid->addColumnText('size', 'Velikost')
			->setRenderer(function (File $file) {
				return $file->getSize();
			});

		$grid->addColumnText('dateAdd', 'Vytvořeno')
			->setSortable()
			->setRenderer(function (File $file) {
				if (empty($file->getDateAdd())) {
					return '---';
				} else {
					return $file->getDateAdd()->format('d.m.Y H:i:s');
				}
			});

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
			/** @var File $file */
			$file = $this->fileManager->getFile($id);
			unlink( FILES_DIR . $file->getName());
			$this->entityManager->remove($file);
			$this->entityManager->flush($file);
		} catch (\Exception $exception) {
			$error = TRUE;
		}

		if ($error) {
			$this->getPresenter()->flashMessage('Soubor se nepodařilo smazat.', 'danger');
		} else {
			$this->getPresenter()->flashMessage('Smazáno');
		}

		if ($this->getPresenter()->isAjax()) {
			$this->getPresenter()->redrawControl('flashes');
			$this['filesGrid']->reload();
		} else {
			$this->redirect('this');
		}
	}
	
	/**
	 * @param string $fileName
	 * @return string
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 * @throws FileException
	 */
	private function checkName(string $fileName): string
	{
		try {
			$file = $this->fileManager->getFileByName($fileName);
			$ext = strtolower(mb_substr($fileName, strrpos($fileName, ".")));
			$name = basename($fileName, $ext);
			return $this->checkName($name.'-'.Random::generate(1, 'a-z') . $ext);
		} catch (FileException $fileException) {
			return $fileName;
		}

	}

}