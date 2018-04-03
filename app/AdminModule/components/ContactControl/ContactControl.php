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

class ContactControl extends Control
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
	 * SettingsControl constructor.
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
		$this->setTemplateName('contactControl.latte');
		$this->settingsRepository = $settingsRepository;
		$this->imageManager = $imageManager;
		$this->galleryRepository = $galleryRepository;
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
		$template->setFile($this->getTemplateFullPath());
		$template->render();
	}

	/**
	 * @return DataGrid
	 * @throws \Ublaboo\DataGrid\Exception\DataGridColumnStatusException
	 */
	public function createComponentContactGrid()
	{
		$grid = new DataGrid();

		$grid->setDataSource($this->settingsRepository->getDataGridQuery());
		$grid->setRememberState(FALSE);
		$grid->setPagination(FALSE);

		$grid->addColumnStatus('showMap', 'Zobrazit mapu')
			->addOption(1, 'Zobrazeno')
			->setIcon('check')
			->setClass('btn-success')
			->endOption()
			->addOption(0, 'Skryto')
			->setIcon('close')
			->setClass('btn-danger')
			->endOption()
			->onChange[] = function ($id, $newValue) {

			$settings = $this->settingsRepository->getSettings();
			$newValue = (bool)$newValue;

			if ($newValue) {
				if (!empty($settings->getMapsApiKey())) {
					$settings->setShowMap($newValue);
					$this->entityManager->flush($settings);
				} else {
					$this->getPresenter()->flashMessage('Musíš nastavit Google Maps App Id', 'danger');
					$this->getPresenter()->redrawControl('flashes');
				}
			} else {
				$settings->setShowMap($newValue);
				$this->entityManager->flush($settings);
			}

			if ($this->getPresenter()->isAjax()) {
				$this['contactGrid']->reload();
			} else {
				$this->redirect('this');
			}
		};

		$grid->addColumnStatus('showAddress', 'Zobrazit adresu')
			->addOption(1, 'Zobrazeno')
			->setIcon('check')
			->setClass('btn-success')
			->endOption()
			->addOption(0, 'Skryto')
			->setIcon('close')
			->setClass('btn-danger')
			->endOption()
			->onChange[] = function ($id, $newValue) {

			$settings = $this->settingsRepository->getSettings();
			$newValue = (bool)$newValue;
			$settings->setShowAddress($newValue);
			$this->entityManager->flush($settings);

			if ($this->getPresenter()->isAjax()) {
				$this['contactGrid']->reload();
			} else {
				$this->redirect('this');
			}
		};

		$grid->addColumnStatus('showTwitterTimeline', 'Twitter timeline')
			->addOption(1, 'Zobrazeno')
			->setIcon('check')
			->setClass('btn-success')
			->endOption()
			->addOption(0, 'Skryto')
			->setIcon('close')
			->setClass('btn-danger')
			->endOption()
			->onChange[] = function ($id, $newValue) {

			$settings = $this->settingsRepository->getSettings();
			$newValue = (bool)$newValue;

			if ($newValue) {
				if (!empty($settings->getTwitter())) {
					$settings->setShowTwitterTimeline($newValue);
					$this->entityManager->flush($settings);
				} else {
					$this->getPresenter()->flashMessage('Nejdříve nastav svůj twitter účet v nastavení obsahu.', 'danger');
					$this->getPresenter()->redrawControl('flashes');
				}
			} else {
				$settings->setShowTwitterTimeline($newValue);
				$this->entityManager->flush($settings);
			}

			$this->entityManager->flush($settings);

			if ($this->getPresenter()->isAjax()) {
				$this['settingsGrid']->reload();
			} else {
				$this->redirect('this');
			}
		};


		return $grid;
	}


	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentContactForm()
	{
		$form = new Form();
		$form->addText('company', 'Firma:');
		$form->addText('ico', 'IČO');
		$form->addText('street', 'Ulice:');
		$form->addText('city', 'Město');
		$form->addText('zip', 'PSČ');
		$form->addText('email', 'E-mail');
		$form->addText('phone', 'Telefon');
		$form->addText('mapsAppId', 'Maps APP ID');
		$form->addText('longitude', 'Zeměpisná délka:');
		$form->addText('latitude', 'Zeměpisná šířka');

		$form->addSubmit('save', 'Uložit');

		$settings = $this->settingsRepository->getSettings();
		$form->setDefaults([
			'company'       => $settings->getCompany(),
			'ico'           => $settings->getIco(),
			'street'        => $settings->getStreet(),
			'city'          => $settings->getCity(),
			'zip'           => $settings->getZip(),
			'email'         => $settings->getEmail(),
			'phone'         => $settings->getPhone(),
			'longitude'     => $settings->getLongitude(),
			'latitude'      => $settings->getLatitude(),
			'mapsAppId'     => $settings->getMapsApiKey(),
		]);

		$form->onSuccess[] = [$this, 'contactFormSucceeded'];
		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 * @param                            $values
	 * @throws \Nette\Application\AbortException
	 */
	public function contactFormSucceeded(Form $form, $values)
	{
		$badMessage = FALSE;
		try {
			$settings = $this->settingsRepository->getSettings();
			$settings->setCompany($values->company);
			$settings->setIco((int)$values->ico);
			$settings->setStreet($values->street);
			$settings->setCity($values->city);
			$settings->setZip((int)$values->zip);
			$settings->setEmail($values->email);
			$settings->setPhone($values->phone);
			$settings->setLongitude((float)$values->longitude);
			$settings->setLatitude((float)$values->latitude);
			$settings->setMapsApiKey($values->mapsAppId);
			$this->entityManager->flush($settings);
		} catch (\Exception $exception) {
			Debugger::log($exception, ILogger::ERROR);
			$this->getPresenter()->flashMessage('Nepodařilo se uložit základní údaje o kontatkech', 'danger');
			$this->getPresenter()->redirect('this');
			$badMessage = TRUE;
		}

		if (!$badMessage) {
			$this->getPresenter()->flashMessage('Uloženo');
			$this->getPresenter()->redirect('this');
		}
	}





}