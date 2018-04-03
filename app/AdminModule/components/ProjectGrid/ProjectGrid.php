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
use Nette\Utils\Html;
use Ublaboo\DataGrid\DataGrid;
use Vojtars\Model\Project;
use Vojtars\Model\ProjectManager;

class ProjectGrid extends Control
{
	use OwnTemplate;

	/**
	 * @var array
	 */
	public $onChange = [];

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var ProjectManager
	 */
	private $projectManager;


	/**
	 * ProjectGrid constructor.
	 * @param EntityManager  $entityManager
	 * @param ProjectManager $projectManager
	 */
	public function __construct(EntityManager $entityManager, ProjectManager $projectManager)
	{
		parent::__construct();
		$this->entityManager = $entityManager;
		$this->setTemplateName('projectGrid.latte');
		$this->projectManager = $projectManager;
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
	 * @return DataGrid
	 * @throws \Ublaboo\DataGrid\Exception\DataGridColumnStatusException
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
	 */
	public function createComponentProjectGrid()
	{
		$grid = new DataGrid();

		$grid->setDataSource($this->projectManager->getDataGridQuery());
		$grid->setItemsPerPageList([20, 50, 100, 200]);
		$grid->setRememberState(FALSE);

		$grid->addColumnText('id', 'Id')
			->setSortable();

		$grid->addColumnText('image', 'Náhled')
			->setSortable()
			->setRenderer(function (Project $project) {
				if (empty($project->getImage())) {
					return NULL;
				} else {
					return Html::el('img')
						->setAttribute('style', 'max-width: 128px; max-height: 128px;')
						->setAttribute('src', '/' . $project->getImage()->getMiniNameWithPath())
						->setAttribute('alt', $project->getImage()->getDescription());
				}

			});

		$grid->addColumnText('name', 'Název')
			->setSortable()
			->setEditableCallback(function ($id, $value) {
				$project = $this->projectManager->getProject((int)$id);
				$project->setName($value);
				$this->entityManager->flush($project);
			});

		$grid->addColumnText('description', 'Popis')
			->setSortable()
			->setEditableCallback(function ($id, $value) {
				$project = $this->projectManager->getProject((int)$id);
				$project->setDescription($value);
				$this->entityManager->flush($project);
			});

		$grid->addColumnText('position', 'Pozice')
			->setSortable()
			->setEditableCallback(function ($id, $value) {
				$project = $this->projectManager->getProject((int)$id);
				$project->setPosition((int)$value);
				$this->entityManager->flush($project);
			});

		$grid->addColumnStatus('showTab1', 'Tab1')
			->setSortable()
			->addOption(1, 'Aktivní')
			->setIcon('check')
			->setClass('btn-success')
			->endOption()
			->addOption(0, 'Neaktivní')
			->setIcon('close')
			->setClass('btn-danger')
			->endOption()
			->onChange[] = function ($id, $newValue) {

			$project = $this->projectManager->getProject((int)$id);
			$project->setShowTab1((bool)$newValue);
			$this->entityManager->flush($project);

			if ($this->getPresenter()->isAjax()) {
				$this['projectGrid']->reload();
			} else {
				$this->redirect('this');
			}
		};

		$grid->addColumnStatus('showTab2', 'Tab2')
			->setSortable()
			->addOption(1, 'Aktivní')
			->setIcon('check')
			->setClass('btn-success')
			->endOption()
			->addOption(0, 'Neaktivní')
			->setIcon('close')
			->setClass('btn-danger')
			->endOption()
			->onChange[] = function ($id, $newValue) {

			$project = $this->projectManager->getProject((int)$id);
			$project->setShowTab2((bool)$newValue);
			$this->entityManager->flush($project);

			if ($this->getPresenter()->isAjax()) {
				$this['projectGrid']->reload();
			} else {
				$this->redirect('this');
			}
		};

		$grid->addColumnStatus('showTab3', 'Tab3')
			->setSortable()
			->addOption(1, 'Aktivní')
			->setIcon('check')
			->setClass('btn-success')
			->endOption()
			->addOption(0, 'Neaktivní')
			->setIcon('close')
			->setClass('btn-danger')
			->endOption()
			->onChange[] = function ($id, $newValue) {

			$project = $this->projectManager->getProject((int)$id);
			$project->setShowTab3((bool)$newValue);
			$this->entityManager->flush($project);

			if ($this->getPresenter()->isAjax()) {
				$this['projectGrid']->reload();
			} else {
				$this->redirect('this');
			}
		};

		$grid->addColumnText('dateAdd', 'Vytvořeno')
			->setSortable()
			->setRenderer(function (Project $project) {
				if (empty($project->getDateAdd())) {
					return '---';
				} else {
					return $project->getDateAdd()->format('d.m.Y H:i:s');
				}
			});

		$grid->addColumnStatus('active', 'Aktivní')
			->setSortable()
			->addOption(1, 'Aktivní')
			->setIcon('check')
			->setClass('btn-success')
			->endOption()
			->addOption(0, 'Neaktivní')
			->setIcon('close')
			->setClass('btn-danger')
			->endOption()
			->onChange[] = function ($id, $newValue) {

			$project = $this->projectManager->getProject((int)$id);
			$project->setActive((bool)$newValue);
			$this->entityManager->flush($project);

			if ($this->getPresenter()->isAjax()) {
				$this['projectGrid']->reload();
			} else {
				$this->redirect('this');
			}
		};

		$grid->addAction('edit', 'Upravit', 'edit!')
			->setIcon('edit')
			->setTitle('Upravit')
			->setClass('btn btn-xs btn-success');

		return $grid;
	}

	/**
	 * @param int $id
	 * @throws \Nette\Application\AbortException
	 */
	public function handleEdit(int $id)
	{
		$this->getPresenter()->redirect('Project:detail', $id);
	}

}