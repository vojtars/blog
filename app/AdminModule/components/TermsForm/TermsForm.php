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

class TermsForm extends Control
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
	 * @var Settings
	 */
	private $settings;

	/**
	 * TermsForm constructor.
	 * @param SettingsRepository $settingsRepository
	 * @param EntityManager      $entityManager
	 */
	public function __construct(SettingsRepository $settingsRepository, EntityManager $entityManager)
	{
		parent::__construct();
		$this->setTemplateName('termsForm.latte');
		$this->settingsRepository = $settingsRepository;
		$this->entityManager = $entityManager;
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
	protected function createComponentTermsForm()
	{
		$this->settings = $this->settingsRepository->getSettings();
		$form = new Form();
		$form->addTextArea('terms', 'Zpracování osobních údajů');
		$form->addSubmit('create', 'Uložit');

		$form->setDefaults([
			'terms'          => $this->settings->getTerms(),
		]);

		$form->onSuccess[] = [$this, 'termsFormSucceeded'];
		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 * @param                            $values
	 * @throws \Exception
	 * @throws \Nette\Application\AbortException
	 */
	public function termsFormSucceeded(Form $form, $values)
	{
		$this->settings = $this->settingsRepository->getSettings();
		$this->settings->setTerms($values->terms);
		$this->entityManager->flush($this->settings);
		$this->getPresenter()->flashMessage('Stránka Zpracování osobních údajů uložena');
		$this->getPresenter()->redirect('Settings:terms');
	}


}