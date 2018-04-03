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
use Vojtars\Model\HomepageManager;
use Vojtars\Model\Project;

class HomepageGrid extends Control
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
	 * @var HomepageManager
	 */
	private $homepageManager;


	/**
	 * HomepageGrid constructor.
	 * @param EntityManager   $entityManager
	 * @param HomepageManager $homepageManager
	 */
	public function __construct(EntityManager $entityManager, HomepageManager $homepageManager)
	{
		parent::__construct();
		$this->entityManager = $entityManager;
		$this->setTemplateName('homepageGrid.latte');
		$this->homepageManager = $homepageManager;
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
	public function createComponentHomepageGrid()
	{
		$grid = new DataGrid();

		$grid->setDataSource($this->homepageManager->getDataGridQuery());
		$grid->setItemsPerPageList([20, 50, 100, 200]);
		$grid->setRememberState(FALSE);

		$grid->addColumnText('id', 'Id')
			->setSortable();

		$grid->addColumnText('name', 'Název')
			->setSortable()
			->setEditableCallback(function ($id, $value) {
				$homepage = $this->homepageManager->getHomepageBlock((int)$id);
				$homepage->setName($value);
				$this->entityManager->flush($homepage);
			});

		$grid->addColumnText('position', 'Pozice')
			->setSortable()
			->setEditableCallback(function ($id, $value) {
				$homepage = $this->homepageManager->getHomepageBlock((int)$id);
				$homepage->setPosition((int)$value);
				$this->entityManager->flush($homepage);
			});

		$grid->addColumnStatus('active', 'Zobrazit')
			->setSortable()
			->addOption(1, 'Zobrazit')
			->setIcon('check')
			->setClass('btn-success')
			->endOption()
			->addOption(0, 'Skrýt')
			->setIcon('close')
			->setClass('btn-danger')
			->endOption()
			->onChange[] = function ($id, $newValue) {

			$homepage = $this->homepageManager->getHomepageBlock((int)$id);
			$homepage->setActive((bool)$newValue);
			$this->entityManager->flush($homepage);

			if ($this->getPresenter()->isAjax()) {
				$this['homepageGrid']->reload();
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
		$this->getPresenter()->redirect('Homepage:edit', $id);
	}

}