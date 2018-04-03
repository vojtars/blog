<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace AdminModule\Presenters;

use AdminModule\Components\BlogForm;
use AdminModule\Components\BlogGrid;
use AdminModule\Components\IBlogFormFactory;
use AdminModule\Components\IBlogGridFactory;
use AdminModule\Components\IProjectFormFactory;
use AdminModule\Components\IProjectGridFactory;
use AdminModule\Components\ProjectForm;
use AdminModule\Components\ProjectGrid;
use Kdyby\Doctrine\EntityManager;
use Nette;
use Tracy\Debugger;
use Vojtars\Model;

class ProjectPresenter extends BasePresenter
{
	/**
	 * @var IProjectGridFactory
	 */
	private $projectGridFactory;

	/**
	 * @var IProjectFormFactory
	 */
	private $projectFormFactory;

	/**
	 * @var Model\ProjectManager
	 */
	private $projectManager;

	/**
	 * @var Model\Project|NULL
	 */
	private $project = NULL;

	/**
	 * @var Model\Settings
	 */
	private $settings;
	/**
	 * @var EntityManager
	 */
	private $entityManager;


	/**
	 * ProjectPresenter constructor.
	 * @param IProjectGridFactory  $projectGridFactory
	 * @param IProjectFormFactory  $projectFormFactory
	 * @param Model\ProjectManager $projectManager
	 * @param EntityManager        $entityManager
	 */
	public function __construct(IProjectGridFactory $projectGridFactory, IProjectFormFactory $projectFormFactory,
	                            Model\ProjectManager $projectManager, EntityManager $entityManager)
	{
		parent::__construct();

		$this->projectGridFactory = $projectGridFactory;
		$this->projectFormFactory = $projectFormFactory;
		$this->projectManager = $projectManager;
		$this->entityManager = $entityManager;
	}

	public function actionDetail(int $id = NULL)
	{
		if (!empty($id))
			$this->project = $this->projectManager->getProject($id);
	}

	public function actionDefault()
	{
		$this->template->settings = $this->settings =  $this->settingsRepository->getSettings();
	}

	public function renderDetail(int $id = NULL)
	{

	}

	/**
	 * @return \AdminModule\Components\ProjectGrid
	 */
	protected function createComponentProjectGrid(): ProjectGrid
	{
		return $this->projectGridFactory->create();
	}

	/**
	 * @return ProjectForm
	 */
	protected function createComponentProjectForm(): ProjectForm
	{
		$projectForm = $this->projectFormFactory->create();
		$projectForm->setProject($this->project);
		$projectForm->setUser($this->userEntity);
		return $projectForm;
	}

	/**
	 * @throws \Exception
	 */
	public function handleChangeShowProjects()
	{
		if ($this->settings->isShowProjects())
			$this->settings->setShowProjects(FALSE);
		else
			$this->settings->setShowProjects(TRUE);

		$this->entityManager->flush($this->settings);
		$this->redrawControl('btns');
	}
}


 