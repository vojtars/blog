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
use Nette\Http\FileUpload;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;
use Tracy\ILogger;
use Vojtars\Model\Gallery;
use Vojtars\Model\GalleryRepository;
use Vojtars\Model\Image;
use Vojtars\Model\ImageManager;
use Vojtars\Model\Project;
use Vojtars\Model\ProjectManager;
use Vojtars\Model\User;

class ProjectForm extends Control
{
	use OwnTemplate;


	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var GalleryRepository
	 */
	private $galleryRepository;

	/**
	 * @var ImageManager
	 */
	private $imageManager;

	/**
	 * @var User
	 */
	private $userEntity;

	/**
	 * @var Project|NULL
	 */
	private $project;

	/**
	 * @var ProjectManager
	 */
	private $projectManager;

	/**
	 * ProjectForm constructor.
	 * @param EntityManager     $entityManager
	 * @param GalleryRepository $galleryRepository
	 * @param ImageManager      $imageManager
	 * @param ProjectManager    $projectManager
	 */
	public function __construct(EntityManager $entityManager, GalleryRepository $galleryRepository,
	                            ImageManager $imageManager, ProjectManager $projectManager)
	{
		parent::__construct();
		$this->entityManager = $entityManager;
		$this->setTemplateName('projectForm.latte');
		$this->galleryRepository = $galleryRepository;
		$this->imageManager = $imageManager;
		$this->projectManager = $projectManager;
	}

	/**
	 * @param Project|null $project
	 */
	public function setProject(Project $project = NULL)
	{
		$this->project = $project;
	}

	public function setUser(User $user)
	{
		$this->userEntity = $user;
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
	protected function createComponentProjectForm()
	{
		$form = new Form();
		$form->addHidden('id', empty($this->project) ? NULL : $this->project->getId());
		$form->addText('name', 'Název:')
			->setRequired('Zadejte název.');
		$form->addText('nameTab1', 'Název Tab1:');
		$form->addText('nameTab2', 'Název Tab2:');
		$form->addText('nameTab3', 'Název Tab3:');
		$form->addTextArea('description', 'Popis');
		$form->addTextArea('tab1', 'Tab1');
		$form->addTextArea('tab2', 'Tab2');
		$form->addTextArea('tab3', 'Tab3');
		$form->addUpload('image', 'Hlavní fotka');
		$form->addSubmit('create', 'Uložit');

		if (!empty($this->project)) {
			$form->setDefaults([
				'name'          => $this->project->getName(),
				'nameTab1'      => $this->project->getNameTab1(),
				'nameTab2'      => $this->project->getNameTab2(),
				'nameTab3'      => $this->project->getNameTab3(),
				'tab1'          => $this->project->getTab1(),
				'tab2'          => $this->project->getTab2(),
				'tab3'          => $this->project->getTab3(),
				'description'   => $this->project->getDescription(),
			]);
		}

		$form->onSuccess[] = [$this, 'projectFormSucceeded'];
		return $form;
	}

	/**
	 * @param \Nette\Application\UI\Form $form
	 * @param                            $values
	 * @throws \Nette\Application\AbortException
	 */
	public function projectFormSucceeded(Form $form, $values)
	{
		if (empty($values->id)) {
			$this->addNewProject($values);
		} else {
			$this->editActualProject($values);
		}
	}

	/**
	 * @param \Nette\Utils\ArrayHash $values
	 * @throws \Nette\Application\AbortException
	 */
	private function addNewProject(ArrayHash $values)
	{
		$badImageMessage = FALSE;
		try {
			$newProject = new Project($values->name);
			$newProject->setTabs($values->tab1, $values->tab2, $values->tab3);
			$newProject->setTabNames($values->nameTab1, $values->nameTab2, $values->nameTab3);
			$newProject->setActive(FALSE);
			$newProject->setDescription($values->description);

			if (!empty($values->image) && (($values->image instanceof FileUpload) && (!empty($values->image->name)))) {
				/** @var Gallery $gallery */
				$gallery = $this->galleryRepository->getDefaultGallery();
				/** @var Image $newImage */
				$newImage = $this->imageManager->saveImage($values->image, $this->userEntity, $gallery, $values->name);
				$newProject->setImage($newImage);
				if (empty($newImage)) {
					$badImageMessage = TRUE;
				}
			}

			$this->entityManager->persist($newProject);
			$this->entityManager->flush($newProject);
		} catch (\Exception $exception) {
			Debugger::log($exception, ILogger::ERROR);
			$this->getPresenter()->flashMessage('Nedařilo se přidat projekt', 'danger');
			$this->getPresenter()->redirect('this');
		}

		if ($badImageMessage) {
			$this->getPresenter()->flashMessage('Projekt přidán, ale obrázek se nepodařilo nahrát');
			$this->getPresenter()->redirect('this');
		} else {
			$this->getPresenter()->flashMessage('Projekt přidán');
			$this->getPresenter()->redirect('Project:default');
		}
	}

	/**
	 * @param \Nette\Utils\ArrayHash $values
	 * @throws \Nette\Application\AbortException
	 */
	private function editActualProject(ArrayHash $values)
	{
		$badImageMessage = FALSE;
		try {
			$project = $this->projectManager->getProject((int)$values->id);
			$project->setTabs($values->tab1, $values->tab2, $values->tab3);
			$project->setTabNames($values->nameTab1, $values->nameTab2, $values->nameTab3);
			$project->setDescription($values->description);
			$project->setName($values->name);

			if (!empty($values->image) && (($values->image instanceof FileUpload) && (!empty($values->image->name)))) {
				/** @var Gallery $gallery */
				$gallery = $this->galleryRepository->getDefaultGallery();
				/** @var Image $newImage */
				$newImage = $this->imageManager->saveImage($values->image, $this->userEntity, $gallery, $values->name);
				$project->setImage($newImage);
				if (empty($newImage)) {
					$badImageMessage = TRUE;
				}
			}
			$this->entityManager->flush($project);
		} catch (\Exception $exception) {
			Debugger::log($exception, ILogger::ERROR);
			$this->getPresenter()->flashMessage('Nedařilo se uložit projekt', 'danger');
			$this->getPresenter()->redirect('this');
		}

		if ($badImageMessage) {
			$this->getPresenter()->flashMessage('Projekt uložen, ale obrázek se nepodařilo nahrát');
			$this->getPresenter()->redirect('this');
		} else {
			$this->getPresenter()->flashMessage('Projekt uložen');
			$this->getPresenter()->redirect('Project:default');

		}
	}
}