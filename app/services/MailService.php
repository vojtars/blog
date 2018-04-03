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

class MailService
{

	/**
	 * @var IMailer
	 */
	private $mailer;

	/**
	 * NotificationService constructor.
	 * @param IMailer $mailer
	 */
	public function __construct(IMailer $mailer)
	{
		$this->mailer = $mailer;
	}

	/**
	 * @param string $subject
	 * @param string $htmlMessage
	 * @param string $mailFrom
	 * @param string $mailTo
	 */
	public function sendBasicEmail(string $subject, string $htmlMessage, string $mailFrom, string $mailTo)
	{
		$mail = new Message;
		$mail->setFrom($mailFrom)
			->addTo($mailTo)
			->setSubject($subject)
			->setHtmlBody($htmlMessage);

		if (AppService::isDevel()) {
			$this->mailer->send($mail);
		} else {
			$mailer = new SendmailMailer;
			$mailer->send($mail);
		}
	}
}