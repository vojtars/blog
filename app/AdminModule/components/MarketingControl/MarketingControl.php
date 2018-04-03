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
use Tracy\ILogger;
use Ublaboo\DataGrid\DataGrid;
use Vojtars\Model\Gallery;
use Vojtars\Model\GalleryRepository;
use Vojtars\Model\Image;
use Vojtars\Model\ImageManager;
use Vojtars\Model\Project;
use Vojtars\Model\ProjectManager;
use Vojtars\Model\SettingsRepository;
use Vojtars\Model\User;

class MarketingControl extends Control
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
	 * MarketingControl constructor.
	 * @param EntityManager      $entityManager
	 * @param SettingsRepository $settingsRepository
	 * @param ImageManager       $imageManager
	 * @param GalleryRepository  $galleryRepository
	 */
	public function __construct(EntityManager $entityManager, SettingsRepository $settingsRepository,
	                            ImageManager $imageManager, GalleryRepository $galleryRepository )
	{
		parent::__construct();
		$this->entityManager = $entityManager;
		$this->setTemplateName('marketingControl.latte');
		$this->settingsRepository = $settingsRepository;
		$this->imageManager = $imageManager;
		$this->galleryRepository = $galleryRepository;
	}

	public function setUser(User $user)
	{
		$this->userEntity = $user;
	}

	/**
	 * @throws \ReflectionException
	 */
	public function render()
	{
		$template = $this->getTemplate();
		$template->settings = $this->settingsRepository->getSettings();
		$template->setFile($this->getTemplateFullPath());
		$template->render();
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentShareSettingsForm()
	{
		$form = new Form();
		$form->addText('title', 'Titulek');
		$form->addText('description', 'Popis');
		$form->addText('keywords', 'Klíčová slova');
		$form->addUpload('image', 'Hlavní fotka');
		$form->addSubmit('create', 'Uložit');

		$settings = $this->settingsRepository->getSettings();
		$form->setDefaults([
			'title'         => $settings->getHeadTitle(),
			'description'   => $settings->getHeadDescription(),
			'keywords'      => $settings->getHeadKeywords()
		]);

		$form->onSuccess[] = [$this, 'shareSettingsFormSucceeded'];
		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 * @param                            $values
	 * @throws \Nette\Application\AbortException
	 */
	public function shareSettingsFormSucceeded(Form $form, $values)
	{
		$badImageMessage = FALSE;
		try {
			$settings = $this->settingsRepository->getSettings();
			$settings->setHeadTitle($values->title);
			$settings->setHeadDescription($values->description);
			$settings->setHeadKeywords($values->keywords);

			if (!empty($values->image) && (($values->image instanceof FileUpload) && (!empty($values->image->name)))) {
				/** @var Gallery $gallery */
				$gallery = $this->galleryRepository->getDefaultGallery();
				/** @var Image $newImage */
				$newImage = $this->imageManager->saveImage($values->image, $this->userEntity, $gallery, $values->title);
				$settings->setHeadImage($newImage);
				if (empty($newImage)) {
					$badImageMessage = TRUE;
				}
			}
			$this->entityManager->flush($settings);
		} catch (\Exception $exception) {
			Debugger::log($exception, ILogger::ERROR);
			$this->getPresenter()->flashMessage('Nepodařilo se uložit základní data ke sdílení', 'danger');
			$this->getPresenter()->redirect('this');
		}

		if ($badImageMessage) {
			$this->getPresenter()->flashMessage('Uloženo, ale obrázek se nepodařilo nahrát');
			$this->getPresenter()->redirect('this');
		} else {
			$this->getPresenter()->flashMessage('Uloženo');
			$this->getPresenter()->redirect('Settings:marketing');
		}
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentScriptsForm()
	{
		$settings = $this->settingsRepository->getSettings();
		$form = new Form();
		$form->addTextArea('head', 'Hlavička:');
		$form->addTextArea('footer', 'Patička:');
		$form->addSubmit('create', 'Uložit');
		$form->setDefaults([
			'head' => $settings->getScriptsHead(),
			'footer' => $settings->getScriptsFooter(),
		]);
		$form->onSuccess[] = [$this, 'scriptsFormSucceeded'];
		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 * @param                            $values
	 * @throws \Nette\Application\AbortException
	 */
	public function scriptsFormSucceeded(Form $form, $values)
	{
		try {
			$settings = $this->settingsRepository->getSettings();
			$settings->setScriptsHead($values->head);
			$settings->setScriptsFooter($values->footer);
			$this->entityManager->flush($settings);
		} catch (\Exception $exception) {
			Debugger::log($exception, ILogger::ERROR);
			$this->getPresenter()->flashMessage('Nedařilo se uložit blok hlavní stránky', 'danger');
			$this->getPresenter()->redirect('this');
		}

		$this->getPresenter()->flashMessage('Blok upraven');
		$this->getPresenter()->redirect('Homepage:list');
	}






}