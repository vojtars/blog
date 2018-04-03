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
use Tracy\Debugger;
use Tracy\ILogger;
use Vojtars\Model\Gallery;
use Vojtars\Model\GalleryRepository;
use Vojtars\Model\Image;
use Vojtars\Model\ImageManager;
use Vojtars\Model\Project;
use Vojtars\Model\Settings;
use Vojtars\Model\SettingsRepository;
use Vojtars\Model\User;

class AboutMeForm extends Control
{
	use OwnTemplate;

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
	private $userEntity;

	/**
	 * @var SettingsRepository
	 */
	private $settingsRepository;

	/**
	 * @var Settings
	 */
	private $settings;

	/**
	 * AboutMeForm constructor.
	 * @param EntityManager      $entityManager
	 * @param GalleryRepository  $galleryRepository
	 * @param ImageManager       $imageManager
	 * @param SettingsRepository $settingsRepository
	 */
	public function __construct(EntityManager $entityManager, GalleryRepository $galleryRepository,
	                            ImageManager $imageManager, SettingsRepository $settingsRepository)
	{
		parent::__construct();
		$this->entityManager = $entityManager;
		$this->setTemplateName('aboutMeForm.latte');
		$this->imageManager = $imageManager;
		$this->settingsRepository = $settingsRepository;
		$this->galleryRepository = $galleryRepository;
	}

	public function setSettings(Settings $settings)
	{
		$this->settings = $settings;
	}

	public function setUserEntity(User $user)
	{
		$this->userEntity = $user;
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
	protected function createComponentAboutMeForm()
	{
		$form = new Form();
		$form->addText('name', 'Jméno a Příjmení:');
		$form->addText('menuName', 'Název v hlavičce:');
		$form->addTextArea('description', 'Krátký popois');
		$form->addTextArea('content', 'O mě');
		$form->addUpload('image', 'Hlavní fotka');
		$form->addSubmit('create', 'Uložit');

		$form->setDefaults([
			'menuName'      => $this->settings->getMeMenuName(),
			'name'          => $this->settings->getMeName(),
			'description'   => $this->settings->getMeDescription(),
			'content'       => $this->settings->getMeContent(),
		]);

		$form->onSuccess[] = [$this, 'aboutMeFormSucceeded'];
		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 * @param                            $values
	 * @throws \Nette\Application\AbortException
	 */
	public function aboutMeFormSucceeded(Form $form, $values)
	{
		$badImageMessage = FALSE;
		try {
			$this->settings = $this->settingsRepository->getSettings();
			$this->settings->setMeMenuName($values->menuName);
			$this->settings->setMeName($values->name);
			$this->settings->setMeContent($values->content);
			$this->settings->setMeDescription($values->description);

			if (!empty($values->image) && (($values->image instanceof FileUpload) && (!empty($values->image->name)))) {
				/** @var Gallery $gallery */
				$gallery = $this->galleryRepository->getDefaultGallery();
				/** @var Image $newImage */
				$newImage = $this->imageManager->saveImage($values->image, $this->userEntity, $gallery, $values->name);
				$this->settings->setMeImage($newImage);
				if (empty($newImage)) {
					$badImageMessage = TRUE;
				}
			}
			$this->entityManager->flush($this->settings);
		} catch (\Exception $exception) {
			Debugger::log($exception, ILogger::ERROR);
			$this->getPresenter()->flashMessage('Nedařilo se uložit stránku O mě', 'danger');
			$this->getPresenter()->redirect('this');
		}

		if ($badImageMessage) {
			$this->getPresenter()->flashMessage('Stránka O mě uložena, ale obrázek se nepodařilo nahrát');
			$this->getPresenter()->redirect('this');
		} else {
			$this->getPresenter()->flashMessage('Stránka O mě uložena');
			$this->getPresenter()->redirect('Settings:aboutMe');
		}
	}


}