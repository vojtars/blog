<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace FrontModule\Components;

use AdminModule\Components\OwnTemplate;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Tracy\Debugger;
use Vojtars\Model\Settings;
use Vojtars\Model\SettingsRepository;
use Vojtars\Services\MailService;

/**
 * Class ContactForm
 * @package FrontModule\Components
 * @method onSubmit($message = NULL, $success = TRUE)
 */
class ContactForm extends Control
{
	use OwnTemplate;

	/**
	 * @var callable[]
	 */
	public $onSubmit = [];

	/**
	 * @var MailService
	 */
	private $mailService;

	/**
	 * @var Settings
	 */
	private $settings;
	/**
	 * @var SettingsRepository
	 */
	private $settingsRepository;

	/**
	 * ContactForm constructor.
	 * @param MailService        $mailService
	 * @param SettingsRepository $settingsRepository
	 */
	public function __construct(MailService $mailService, SettingsRepository $settingsRepository)
	{
		parent::__construct();
		$this->setTemplateName('contactForm.latte');
		$this->mailService = $mailService;
		$this->settingsRepository = $settingsRepository;
	}

	/**
	 * @throws \ReflectionException
	 */
	public function render()
	{
		$template = $this->getTemplate();
		$template->settings = empty($this->settings) ? $this->settingsRepository->getSettings() : $this->settings;
		$template->setFile($this->getTemplateFullPath());
		$template->render();
	}


	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentContactForm()
	{
		$form = new Form();
		$form->addText('name', 'Jméno:')
			->setRequired('Zadejte jméno');
		$form->addText('surname', 'Příjmení:')
			->setRequired('Zadejte přijmení');
		$form->addEmail('email', 'Email')
			->setRequired('Zadejte email');
		$form->addTextArea('message', 'Dotaz')
			->setRequired('Zadejte dotaz');
		$form->addSubmit('send', 'Odeslat');
		$form->onSuccess[] = [$this, 'contactFormSucceeded'];
		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 * @param                            $values
	 */
	public function contactFormSucceeded(Form $form, $values)
	{
		try {
			$this->settings = $this->settingsRepository->getSettings();
			$subject = $this->settings->getName().' - kontaktní formulář';
			$message = "<p><b>Jméno:</b> $values->name $values->surname</p><br>";
			$message .= "<p><b>Dotaz:</b><br>$values->message</p><br>";
			$this->mailService->sendBasicEmail($subject, $message, $values->email, $this->settings->getEmail());
			$message = 'Formulář byl úspěšně odeslán.';
			$success = TRUE;
		} catch (\Exception $exception) {
			Debugger::log($exception);
			$message = 'Při odeslání formuláře nastala chyba. Zkuste později.';
			$success = FALSE;
		}
		$this->onSubmit($message, $success);
	}

}

 