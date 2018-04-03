<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace AdminModule\Components;

use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;
use Tracy\ILogger;
use Vojtars\Model\Homepage;
use Vojtars\Model\HomepageManager;

class HomepageForm extends Control
{
	use OwnTemplate;

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var Homepage|NULL
	 */
	private $homepageBlock;

	/**
	 * @var HomepageManager
	 */
	private $homepageManager;

	/**
	 * UserGrid constructor.
	 * @param EntityManager   $entityManager
	 * @param HomepageManager $homepageManager
	 */
	public function __construct(EntityManager $entityManager, HomepageManager $homepageManager)
	{
		parent::__construct();
		$this->entityManager = $entityManager;
		$this->setTemplateName('homepageForm.latte');
		$this->homepageManager = $homepageManager;
	}

	/**
	 * @param Homepage|null $homepage
	 */
	public function setHomepageBlock(Homepage $homepage = NULL)
	{
		$this->homepageBlock = $homepage;
	}

	/**
	 * @throws \ReflectionException
	 */
	public function render()
	{
		$template = $this->getTemplate();
		$template->setFile($this->getTemplateFullPath());
		$template->render();
	}

	/**
	 * @return \Nette\Application\UI\Form
	 */
	protected function createComponentHomepageForm()
	{
		$form = new Form();
		$form->addHidden('id', empty($this->homepageBlock) ? NULL : $this->homepageBlock->getId());
		$form->addText('name', 'Název:');
		$form->addTextArea('content', 'Obsah:');
		$form->addSubmit('create', 'Uložit');

		if (!empty($this->homepageBlock)) {
			$form->setDefaults([
				'name' => $this->homepageBlock->getName(),
				'content' => $this->homepageBlock->getContent(),
			]);
		}

		$form->onSuccess[] = [$this, 'homepageFormSucceeded'];
		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 * @param                            $values
	 * @throws \Nette\Application\AbortException
	 */
	public function homepageFormSucceeded(Form $form, $values)
	{
		if (empty($values->id)) {
			$this->addNewHomepage($values);
		} else {
			$this->editActualHomepage($values);
		}
	}

	/**
	 * @param \Nette\Utils\ArrayHash $values
	 * @throws \Nette\Application\AbortException
	 */
	private function addNewHomepage(ArrayHash $values)
	{
		try {
			$newHomepage = new Homepage($values->name, FALSE);
			if (!empty($values->content))
				$newHomepage->setContent($values->content);

			$this->entityManager->persist($newHomepage);
			$this->entityManager->flush($newHomepage);
		} catch (\Exception $exception) {
			Debugger::log($exception, ILogger::ERROR);
			$this->getPresenter()->flashMessage('Nedařilo se přidat blok hlavní stránky', 'danger');
			$this->getPresenter()->redirect('this');
		}

		$this->getPresenter()->flashMessage('Blok přidán');
		$this->getPresenter()->redirect('Homepage:list');

	}

	/**
	 * @param \Nette\Utils\ArrayHash $values
	 * @throws \Nette\Application\AbortException
	 */
	private function editActualHomepage(ArrayHash $values)
	{
		try {
			$homepage = $this->homepageManager->getHomepageBlock((int)$values->id);
			$homepage->setName($values->name);
			$homepage->setContent($values->content);
			$this->entityManager->flush($homepage);
		} catch (\Exception $exception) {
			Debugger::log($exception, ILogger::ERROR);
			$this->getPresenter()->flashMessage('Nedařilo se uložit blok hlavní stránky', 'danger');
			$this->getPresenter()->redirect('this');
		}

		$this->getPresenter()->flashMessage('Blok upraven');
		$this->getPresenter()->redirect('Homepage:list');
	}
}