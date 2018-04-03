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

class SettingsControl extends Control
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
		$this->setTemplateName('settingsControl.latte');
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
	public function createComponentSocialsGrid()
	{
		$grid = new DataGrid();

		$grid->setDataSource($this->settingsRepository->getDataGridQuery());
		$grid->setRememberState(FALSE);
		$grid->setPagination(FALSE);

		$grid->addColumnStatus('showFacebook', 'Facebook')
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
				if (!empty($settings->getFbAppId())) {
					$settings->setShowFacebook($newValue);
					$this->entityManager->flush($settings);
				} else {
					$this->getPresenter()->flashMessage('Musíš nastavit FB App Id', 'danger');
					$this->getPresenter()->redrawControl('flashes');

				}
			} else {
				$settings->setShowFacebook($newValue);
				$this->entityManager->flush($settings);
			}

			if ($this->getPresenter()->isAjax()) {
				$this['socialsGrid']->reload();
			} else {
				$this->redirect('this');
			}
		};

		$grid->addColumnStatus('showTwitter', 'Twitter')
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
					$settings->setShowTwitter($newValue);
					$this->entityManager->flush($settings);
				} else {
					$this->getPresenter()->flashMessage('Nemáš nastavený Twitter účet.', 'danger');
					$this->getPresenter()->redrawControl('flashes');
				}
			} else {
				$settings->setShowTwitter($newValue);
				$this->entityManager->flush($settings);
			}

			if ($this->getPresenter()->isAjax()) {
				$this['socialsGrid']->reload();
			} else {
				$this->redirect('this');
			}
		};

		$grid->addColumnStatus('showInstagram', 'Instagram')
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
			$settings->setShowInstagram((bool)$newValue);
			$this->entityManager->flush($settings);

			if ($this->getPresenter()->isAjax()) {
				$this['socialsGrid']->reload();
			} else {
				$this->redirect('this');
			}
		};

		$grid->addColumnStatus('showLinkedIn', 'LinkedIn')
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
			$settings->setShowLinkedIn((bool)$newValue);
			$this->entityManager->flush($settings);

			if ($this->getPresenter()->isAjax()) {
				$this['socialsGrid']->reload();
			} else {
				$this->redirect('this');
			}
		};

		$grid->addColumnStatus('showGithub', 'GitHub')
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
			$settings->setShowGithub((bool)$newValue);
			$this->entityManager->flush($settings);

			if ($this->getPresenter()->isAjax()) {
				$this['socialsGrid']->reload();
			} else {
				$this->redirect('this');
			}
		};

		return $grid;
	}


	/**
	 * @return DataGrid
	 * @throws \Ublaboo\DataGrid\Exception\DataGridColumnStatusException
	 */
	public function createComponentSettingsGrid()
	{
		$grid = new DataGrid();

		$grid->setDataSource($this->settingsRepository->getDataGridQuery());
		$grid->setRememberState(FALSE);
		$grid->setPagination(FALSE);

		$grid->addColumnStatus('showComments', 'Facebook komentáře v blogu')
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
				if (!empty($settings->getFbAppId())) {
					$settings->setShowComments($newValue);
					$this->entityManager->flush($settings);
				} else {
					$this->getPresenter()->flashMessage('Musíš nastavit FB APP ID.', 'danger');
					$this->getPresenter()->redrawControl('flashes');
				}
			} else {
				$settings->setShowComments($newValue);
				$this->entityManager->flush($settings);
			}

			if ($this->getPresenter()->isAjax()) {
				$this['settingsGrid']->reload();
			} else {
				$this->redirect('this');
			}
		};

		$grid->addColumnStatus('shareFacebook', 'Sdílení přes Facebook')
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
				if (!empty($settings->getFbAppId())) {
					$settings->setShareFacebook($newValue);
					$this->entityManager->flush($settings);
				} else {
					$this->getPresenter()->flashMessage('Musíš nastavit FB APP ID.', 'danger');
					$this->getPresenter()->redrawControl('flashes');
				}
			} else {
				$settings->setShareFacebook($newValue);
				$this->entityManager->flush($settings);
			}

			if ($this->getPresenter()->isAjax()) {
				$this['settingsGrid']->reload();
			} else {
				$this->redirect('this');
			}
		};

		$grid->addColumnStatus('shareTwitter', 'Sdílení přes Twitter')
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
			$settings->setShareTwitter((bool)$newValue);
			$this->entityManager->flush($settings);

			if ($this->getPresenter()->isAjax()) {
				$this['settingsGrid']->reload();
			} else {
				$this->redirect('this');
			}
		};

		$grid->addColumnStatus('sendNewSubscribers', 'Zasílat nové odvěratele')
			->addOption(1, 'Zobrazit')
			->setIcon('check')
			->setClass('btn-success')
			->endOption()
			->addOption(0, 'Skrýt')
			->setIcon('close')
			->setClass('btn-danger')
			->endOption()
			->onChange[] = function ($id, $newValue) {

			$settings = $this->settingsRepository->getSettings();
			$settings->setSendNewSubscribers((bool)$newValue);
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
	protected function createComponentDefaultSettingsForm()
	{
		$colors = [
			'blue-grey' => 'blue-grey',
			'grey'      => 'grey',
			'brown'     => 'brown',
			'deep-orange'   => 'deep-orange',
			'orange'        => 'orange',
			'amber'         => 'amber',
			'yellow'        => 'yellow',
			'lime'          => 'lime',
			'light-green'   => 'light-green',
			'green'         => 'green',
			'teal'          => 'teal',
			'cyan'          => 'cyan',
			'light-blue'    => 'light-blue',
			'blue'          => 'blue',
			'indigo'        => 'indigo',
			'deep-purple'   => 'deep-purple',
			'purple'        => 'purple',
			'pink'          => 'pink',
			'red'           => 'red',
		];
		$form = new Form();
		$form->addText('name', 'Název webu:');
		$form->addText('title1', 'Popis 1');
		$form->addText('title2', 'Popis 2');
		$form->addInteger('ico', 'IČO');
		$form->addSelect('color', 'Barva webu', $colors);
		$form->addText('footerText', 'Text v patičce');
		$form->addText('ctaHref', 'vlastní Click To Action - odkaz');
		$form->addText('ctaName', 'vlastní Click To Action - název');
		$form->addCheckbox('ctaOwn', 'Zobrazit vlastní Call To Action');
		$form->addUpload('image', 'Hlavní fotka');
		$form->addSubmit('create', 'Uložit');

		$settings = $this->settingsRepository->getSettings();
		$form->setDefaults([
			'name'          => $settings->getName(),
			'title1'        => $settings->getTitle1(),
			'title2'        => $settings->getTitle2(),
			'ico'           => $settings->getIco(),
			'footerText'    => $settings->getFooterText(),
			'color'         => $settings->getContentColor(),
			'ctaHref'       => $settings->getCtaHref(),
			'ctaOwn'        => $settings->isCtaOwn(),
			'ctaName'       => $settings->getCtaName(),
		]);

		$form->onSuccess[] = [$this, 'defaultSettingsFormSucceeded'];
		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 * @param                            $values
	 * @throws \Nette\Application\AbortException
	 */
	public function defaultSettingsFormSucceeded(Form $form, $values)
	{
		$badImageMessage = FALSE;
		try {
			$settings = $this->settingsRepository->getSettings();
			$settings->setName($values->name);
			$settings->setTitle1($values->title1);
			$settings->setTitle2($values->title2);
			$settings->setIco((int)$values->ico);
			$settings->setFooterText($values->footerText);
			$settings->setContentColor($values->color);
			$settings->setCtaOwn((bool)$values->ctaOwn);
			$settings->setCtaHref($values->ctaHref);
			$settings->setCtaName($values->ctaName);

			if (!empty($values->image) && (($values->image instanceof FileUpload) && (!empty($values->image->name)))) {
				/** @var Gallery $gallery */
				$gallery = $this->galleryRepository->getDefaultGallery();
				/** @var Image $newImage */
				$newImage = $this->imageManager->saveImage($values->image, $this->userEntity, $gallery, $values->name);
				$settings->setImage($newImage);
				if (empty($newImage)) {
					$badImageMessage = TRUE;
				}
			}
			$this->entityManager->flush($settings);
		} catch (\Exception $exception) {
			Debugger::log($exception, ILogger::ERROR);
			$this->getPresenter()->flashMessage('Nepodařilo se uložit základní údaje o webu', 'danger');
			$this->getPresenter()->redirect('this');
		}

		if ($badImageMessage) {
			$this->getPresenter()->flashMessage('Uloženo, ale obrázek se nepodařilo nahrát');
			$this->getPresenter()->redirect('this');
		} else {
			$this->getPresenter()->flashMessage('Uloženo');
			$this->getPresenter()->redirect('Settings:default');
		}
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentSocialsForm()
	{
		$form = new Form();
		$form->addText('fbAppId', 'Facebook App Id:');
		$form->addText('facebook', 'Facebook:');
		$form->addText('twitter', 'Twitter');
		$form->addText('instagram', 'Instagram');
		$form->addText('linkedIn', 'LinkedIn');
		$form->addText('github', 'GitHub');
		$form->addSubmit('create', 'Uložit');

		$settings = $this->settingsRepository->getSettings();
		$form->setDefaults([
			'fbAppId'          => $settings->getFbAppId(),
			'facebook'          => $settings->getFacebook(),
			'twitter'           => $settings->getTwitter(),
			'instagram'         => $settings->getInstagram(),
			'linkedIn'          => $settings->getInstagram(),
			'github'            => $settings->getGithub(),
		]);

		$form->onSuccess[] = [$this, 'socialFormSucceeded'];
		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 * @param                            $values
	 * @throws \Nette\Application\AbortException
	 */
	public function socialFormSucceeded(Form $form, $values)
	{
		$badImageMessage = FALSE;
		try {
			$settings = $this->settingsRepository->getSettings();
			$settings->setFacebook($values->facebook);
			$settings->setTwitter($values->twitter);
			$settings->setInstagram($values->instagram);
			$settings->setGithub($values->github);
			$settings->setLinkedIn($values->linkedIn);
			$settings->setFbAppId($values->fbAppId);
			$this->entityManager->flush($settings);
		} catch (\Exception $exception) {
			Debugger::log($exception, ILogger::ERROR);
			$this->getPresenter()->flashMessage('Nepodařilo se uložit údaje o sicálních sítích', 'danger');
			$this->getPresenter()->redirect('this');
		}

		if ($badImageMessage) {
			$this->getPresenter()->flashMessage('Uloženo');
			$this->getPresenter()->redirect('Settings:default');
		}
	}



}