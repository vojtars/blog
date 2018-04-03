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
use Tracy\Debugger;
use Tracy\ILogger;
use Ublaboo\DataGrid\DataGrid;
use Vojtars\Model\Project;
use Vojtars\Model\ProjectManager;
use Vojtars\Model\Subscriber;
use Vojtars\Model\SubscriberManager;

class SubscriberGrid extends Control
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
	 * @var SubscriberManager
	 */
	private $subscriberManager;


	/**
	 * SubscriberGrid constructor.
	 * @param EntityManager     $entityManager
	 * @param SubscriberManager $subscriberManager
	 */
	public function __construct(EntityManager $entityManager, SubscriberManager $subscriberManager)
	{
		parent::__construct();
		$this->entityManager = $entityManager;
		$this->subscriberManager = $subscriberManager;
		$this->setTemplateName('subscriberGrid.latte');
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
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
	 */
	public function createComponentSubscriberGrid()
	{
		$grid = new DataGrid();

		$grid->setDataSource($this->subscriberManager->getDataGridQuery());
		$grid->setItemsPerPageList([20, 50, 100, 200]);
		$grid->setRememberState(FALSE);

		$grid->addColumnText('id', 'Id')
			->setSortable();

		$grid->addColumnText('blog', 'Blog')
			->setSortable()
			->setRenderer(function (Subscriber $subscriber) {
				return $subscriber->getBlog()->getName();
			});

		$grid->addColumnText('email', 'E-mail')
			->setSortable()
			->setEditableCallback(function ($id, $value) {
				$subscriber = $this->subscriberManager->getSubscriber((int)$id);
				$subscriber->setEmail($value);
				$this->entityManager->flush($subscriber);
			});


		$grid->addColumnText('dateAdd', 'Datum přidání')
			->setSortable()
			->setRenderer(function (Subscriber $subscriber) {
				return $subscriber->getDateAdd()->format('d.m.Y H:i');
			});

		$grid->addExportCsv('Export v CSV', 'subscribers.csv')
			->setTitle('Odběratelé');

		$grid->addAction('delete', 'Smazat', 'delete!')
			->setIcon('remove')
			->setTitle('Smazat')
			->setClass('btn btn-xs btn-danger');

		return $grid;
	}

	/**
	 * @param int $id
	 * @throws \Nette\Application\AbortException
	 */
	public function handleDelete(int $id)
	{
		try {
			$subscriber = $this->subscriberManager->getSubscriber($id);
			$this->entityManager->remove($subscriber);
			$this->entityManager->flush($subscriber);
			$message = 'Odběratel smazán';
			$messageType = 'success';
		} catch (\Exception $exception) {
			Debugger::log($exception, ILogger::CRITICAL);
			$message = 'Odběratele se nepodařilo smazat';
			$messageType = 'danger';
		}

		$this->flashMessage($message, $messageType);
		if ($this->getPresenter()->isAjax()) {
			$this['subscriberGrid']->reload();
		} else {
			$this->redirect('this');
		}
	}

}