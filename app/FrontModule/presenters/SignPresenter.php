<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace FrontModule\Presenters;

use Nette;
use Vojtars\Forms;
use Tracy\Debugger;

class SignPresenter extends BasePresenter
{
	/** 
	 * @var Forms\SignInFormFactory 
	 * @inject 
	 */
	public $signInFactory;

	/** 
	 * @var Forms\SignUpFormFactory 
	 * @inject 
	 */
	public $signUpFactory;
	
	/**
	 * @var Forms\FormFactory
	 */
	private $formFactory;

	/**
	 * @var string
	 */
	private $returnUrl = NULL;

	/**
	 * SignPresenter constructor.
	 * @param Forms\FormFactory $formFactory
	 */
	public function __construct(Forms\FormFactory $formFactory)
	{
		parent::__construct();
		$this->formFactory = $formFactory;
	}

	/**
	 * @param null $returnUrl
	 */
	public function actionIn($returnUrl = NULL)
	{
		$this->returnUrl = $returnUrl;
	}

	/**
	 * Sign-in form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignInForm()
	{
		return $this->signInFactory->create(function () {
			if ($this->returnUrl == 'admin') {
				$this->disallowAjax();
				$this->redirect(':Admin:Homepage:default');
			} else {
				$this->redirect('Homepage:');
			}
		});
	}

	/**
	 * Sign-up form factory.
	 * @return Nette\Application\UI\Form
	 */
	protected function createComponentSignUpForm()
	{
		return $this->signUpFactory->create(function () {
			$this->redirect('Homepage:');
		});
	}

	public function actionOut()
	{
		$this->getUser()->logout();
	}

	protected function createComponentNewPasswordForm()
	{
		$form = $this->formFactory->create();
		$form->addText('email', 'E-mail:')
			->setRequired('Vyplňte e-mail.')
			->addRule($form::EMAIL);

		$form->addSubmit('send', 'Odeslat');
		$form->onSuccess[] = [$this, 'newPassword!'];

		//		$form->onSuccess[] = function (Form $form, $values) use ($onSuccess) {
		//		try {
		//			$this->userManager->add($values->name,$values->surname,$values->email,$values->password,$values->location);
		//		} catch (DuplicateNameException $e) {
		//			$form->addError('Username is already taken.');
		//			return;
		//		}
		//		$onSuccess();
		//	};
		return $form;
	}

	public function handleNewPassword($values)
	{

	}

}
