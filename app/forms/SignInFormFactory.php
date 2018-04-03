<?php

namespace Vojtars\Forms;

use Nette;
use Nette\Application\UI\Form;
use Nette\Security\User;


class SignInFormFactory
{
	use Nette\SmartObject;

	/** @var FormFactory */
	private $factory;

	/** @var User */
	private $user;


	public function __construct(FormFactory $factory, User $user)
	{
		$this->factory = $factory;
		$this->user = $user;
	}


	/**
	 * @return Form
	 */
	public function create(callable $onSuccess)
	{
		$form = $this->factory->create();
		$form->addText('email', 'Email:')
			->setRequired('Zadejte e-mail');

		$form->addPassword('password', 'Heslo:')
			->setRequired('Zadejte heslo');

		$form->addSubmit('send', 'Sign in');

		$form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
			try {
				$this->user->setExpiration( '14 days');
				$this->user->login($values->email, $values->password);
			} catch (Nette\Security\AuthenticationException $e) {
				$form->addError('Zadali jste špatný e-mail nebo heslo.');
				return;
			}
			$onSuccess();
		};
		return $form;
	}

}
