<?php

namespace Vojtars\Forms;

use Vojtars\Model\DuplicateNameException;
use Vojtars\Model\UserManager;
use Nette;
use Nette\Application\UI\Form;


class SignUpFormFactory
{
	use Nette\SmartObject;

	const PASSWORD_MIN_LENGTH = 6;

	/** @var FormFactory */
	private $factory;

	/** @var UserManager */
	private $userManager;


	public function __construct(FormFactory $factory, UserManager $userManager)
	{
		$this->factory = $factory;
		$this->userManager = $userManager;
	}


	/**
	 * @return Form
	 */
	public function create(callable $onSuccess)
	{
		$form = $this->factory->create();
		$form->addText('name', 'Jméno:')
			->setRequired('Vyplňte jméno.');

		$form->addText('surname', 'Příjmení:')
			->setRequired('Vyplňte příjmení.');

		$form->addText('email', 'E-mail:')
			->setRequired('Vyplňte e-mail.')
			->addRule($form::EMAIL);

		$form->addPassword('password', 'Heslo:')
			->setOption('description', sprintf('Heslo musí mít minimálně %d písmen.', self::PASSWORD_MIN_LENGTH))
			->setRequired('Vyplňte heslo.')
			->addRule($form::MIN_LENGTH, NULL, self::PASSWORD_MIN_LENGTH);

		$form->addPassword('password2', 'Znovu heslo::')
			->setOption('description', sprintf('Heslo musí mít minimálně %d písmen.', self::PASSWORD_MIN_LENGTH))
			->setRequired('Vyplňte heslo.')
			->addRule($form::MIN_LENGTH, NULL, self::PASSWORD_MIN_LENGTH);

		$form->addText('location', 'Lokace:')
			->setRequired('Vyplňte lokaci.');

		$form->addSubmit('send', 'Registrovat');

		$form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
			try {
				$this->userManager->add($values->name,$values->surname,$values->email,$values->password);
			} catch (DuplicateNameException $e) {
				$form->addError('Username is already taken.');
				return;
			}
			$onSuccess();
		};
		return $form;
	}

}
