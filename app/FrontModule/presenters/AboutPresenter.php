<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace FrontModule\Presenters;

use FrontModule\Components\IContactFormFactory;
use Vojtars\Model\ProjectManager;

class AboutPresenter extends BasePresenter
{
	/**
	 * @var ProjectManager
	 */
	private $projectManager;
	/**
	 * @var IContactFormFactory
	 */
	private $contactFormFactory;


	/**
	 * AboutPresenter constructor.
	 * @param ProjectManager      $projectManager
	 * @param IContactFormFactory $contactFormFactory
	 */
	public function __construct(ProjectManager $projectManager, IContactFormFactory $contactFormFactory)
	{
		parent::__construct();
		$this->projectManager = $projectManager;
		$this->contactFormFactory = $contactFormFactory;
	}

	/**
	 * @throws \Nette\Application\UI\InvalidLinkException
	 */
	public function actionMe()
	{
		$this->editLink = $this->link(':Admin:Settings:aboutMe');
	}

	/**
	 * @throws \Nette\Application\UI\InvalidLinkException
	 */
	public function actionMyProjects()
	{
		$this->editLink = $this->link(':Admin:Project:default');
	}

	/**
	 * @throws \Nette\Application\UI\InvalidLinkException
	 */
	public function actionContact()
	{
		$this->editLink = $this->link(':Admin:Settings:contacts');
	}

	/**
	 * @throws \Nette\Application\BadRequestException
	 */
	public function renderMe()
	{
		if (!$this->settings->isMeShowPage())
			$this->error('Požadovaná stránka neexistuje.');
	}

	/**
	 * @throws \Nette\Application\BadRequestException
	 */
	public function renderMyProjects()
	{
		if (!$this->settings->isShowProjects())
			$this->error('Požadovaná stránka neexistuje.');

		$this->template->projects = $this->projectManager->getActiveProjects();
	}

	/**
	 * @return \FrontModule\Components\ContactForm
	 */
	protected function createComponentContactForm()
	{
		$contactForm = $this->contactFormFactory->create();
		$contactForm->onSubmit[] = function (string $message, bool $success) {
			if ($success)
				$this->flashMessage($message);
			else
				$this->flashMessage($message, 'danger');

			$this->redrawControl();
		};
		return $contactForm;
	}
}