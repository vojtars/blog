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
 * Class SubscriberForm
 * @package FrontModule\Components
 * @method onSubmit($message = NULL, $success = TRUE)
 */
class SubscriberForm extends Control
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
	 * SubscriberForm constructor.
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
		$this->setTemplateName('subscriberForm.latte');
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


	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentSubscriberForm()
	{
		$blogs = $this->blogRepository->getActiveBlogsForSelectInput();

		$form = new Form();
		$form->addEmail('email', 'Email')
			->setRequired('Zadejte email');
		$form->addSelect('blogs', 'Blogy', $blogs)
			->setRequired('Vyberte blog');
		$form->addCheckbox('confirm', 'Souhlas s podmínkami')
			->setRequired('Musíte souhlasit s podmínkami');
		$form->addSubmit('send', 'Odebírat');
		$form->onSuccess[] = [$this, 'subscriberFormSucceeded'];
		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 * @param                            $values
	 */
	public function subscriberFormSucceeded(Form $form, $values)
	{
		if ($values->confirm) {
			try {
				$blog = $this->blogRepository->getActiveBlog($values->blogs);

				$subscriber = $this->subscriberRepository->findByBlog($blog, $values->email);
				if (empty($subscriber)) {
					$newSubscriber = new Subscriber($values->email, $blog);
					$this->entityManager->persist($newSubscriber);
					$this->entityManager->flush($newSubscriber);

					$this->notificationService->newSubscriber($blog, $values->email);

					$message = 'Přihlášení k odběru proběhlo úspěšně.';
					$success = TRUE;
				} else {
					$message = 'Již odebíráte tento blog, nelze přihlásit odběr.';
					$success = FALSE;
				}

			} catch (\Exception $exception) {
				Debugger::log($exception);
				$message = 'Při přihlášení k odběru došlo k chybě. Zkuste později.';
				$success = FALSE;
			}
		} else {
			$message = 'Musíte souhlasit se zpracováním osobních údajů';
			$success = FALSE;
		}

		$this->onSubmit($message, $success);
	}

}




 