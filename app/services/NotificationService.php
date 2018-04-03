<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Services;

use Nette\Mail\IMailer;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Vojtars\Model\Blog;
use Vojtars\Model\SettingsRepository;

/**
 * Třída na posílání e-mailových notifikací, používám na mapování dění na webu, když chci vědět o konkrétní aktivitě
 *
 * Class NotificationService
 * @package Vojtars\Services
 */
class NotificationService
{

	const NOTIFICATION_MAIL = 'notifikace@email.cz'; // Zadejte vlastní

	/**
	 * @var IMailer
	 */
	private $mailer;
	/**
	 * @var SettingsRepository
	 */
	private $settingsRepository;

	/**
	 * NotificationService constructor.
	 * @param IMailer            $mailer
	 * @param SettingsRepository $settingsRepository
	 */
	public function __construct(IMailer $mailer, SettingsRepository $settingsRepository)
	{
		$this->mailer = $mailer;
		$this->settingsRepository = $settingsRepository;
	}

	/**
	 * @param Blog|NULL $blog
	 * @param string    $email
	 */
	public function newSubscriber(Blog $blog = NULL, string $email)
	{
		$settings = $this->settingsRepository->getSettings();

		if ($settings->isSendNewSubscribers()) {
			$blogName = empty($blog) ? "Všech blogů" : $blog->getName();
			$body = "<p><b>Nový odběratel $blogName:</b> $email</p>";

			$mail = new Message;
			$mail->setFrom($email)
				->addTo($settings->getEmail())
				->setSubject("Nový odběratel")
				->setHtmlBody($body);

			if (AppService::isDevel()) {
				$this->mailer->send($mail);
			} else {
				$mailer = new SendmailMailer;
				$mailer->send($mail);
			}
		}

	}

}