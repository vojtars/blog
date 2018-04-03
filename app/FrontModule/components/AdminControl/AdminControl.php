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
 * Class AdminControl
 * @package FrontModule\Components
 */
class AdminControl extends Control
{
	use OwnTemplate;

	/**
	 * @var string
	 */
	private $link = NULL;

	/**
	 * AdminControl constructor.
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setTemplateName('adminControl.latte');
	}

	public function setLink(string $link = NULL)
	{
		$this->link = $link;
	}

	/**
	 * @throws \ReflectionException
	 */
	public function render()
	{
		$template = $this->getTemplate();
		$template->link = $this->link;
		$template->setFile($this->getTemplateFullPath());
		$template->render();
	}

}




 