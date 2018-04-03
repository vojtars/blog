<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace FrontModule\Components;

use AdminModule\Components\OwnTemplate;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Tracy\Debugger;
use Vojtars\Model\BlogRepository;
use Vojtars\Model\Settings;
use Vojtars\Model\SettingsRepository;
use Vojtars\Model\Subscriber;
use Vojtars\Model\SubscriberRepository;
use Vojtars\Services\MailService;
use Vojtars\Services\NotificationService;

/**
 * Class SearchForm
 * @package FrontModule\Components
 * @method onSubmit($message = NULL, $success = TRUE)
 */
class SearchForm extends Control
{
	use OwnTemplate;

	/**
	 * @var callable[]
	 */
	public $onSubmit = [];

	/**
	 * @var NotificationService
	 */
	private $notificationService;

	/**
	 * @var BlogRepository
	 */
	private $blogRepository;

	/**
	 * @var EntityManager
	 */
	private $entityManager;
	/**
	 * @var SubscriberRepository
	 */
	private $subscriberRepository;
	/**
	 * @var SettingsRepository
	 */
	private $settingsRepository;


	/**
	 * SearchForm constructor.
	 * @param NotificationService  $notificationService
	 * @param SubscriberRepository $subscriberRepository
	 * @param BlogRepository       $blogRepository
	 * @param EntityManager        $entityManager
	 * @param SettingsRepository   $settingsRepository
	 */
	public function __construct(NotificationService $notificationService, SubscriberRepository $subscriberRepository,
	                            BlogRepository $blogRepository, EntityManager $entityManager, SettingsRepository $settingsRepository)
	{
		parent::__construct();
		$this->setTemplateName('searchForm.latte');
		$this->notificationService = $notificationService;
		$this->blogRepository = $blogRepository;
		$this->entityManager = $entityManager;
		$this->subscriberRepository = $subscriberRepository;
		$this->settingsRepository = $settingsRepository;
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


	protected function createComponentSearchForm()
	{
		$form = new Form();
		$form->addText('query', 'Hledaný výraz')
			->setRequired(TRUE);
		$form->addSubmit('send', 'Hledat');
		$form->onSuccess[] = [$this, 'searchFormSucceeded'];
		return $form;
	}

	/**
	 * @param Form $form
	 * @param      $values
	 * @throws \Nette\Application\AbortException
	 */
	public function searchFormSucceeded(Form $form, $values)
	{
		Debugger::barDump($values);
		$this->getPresenter()->redirect('Search:default', $values->query);
	}
}




 