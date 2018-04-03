<?php declare(strict_types=1);
/**
 * Copyright (c) 2018. 
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace AdminModule\Presenters;

use AdminModule\Components\DashboardControl;
use AdminModule\Components\HomepageForm;
use AdminModule\Components\HomepageGrid;
use AdminModule\Components\IDashboardControlFactory;
use AdminModule\Components\IHomepageFormFactory;
use AdminModule\Components\IHomepageGridFactory;
use Nette\Application\UI\Multiplier;
use Vojtars\Model\Homepage;
use Vojtars\Model\HomepageManager;

class HomepagePresenter extends BasePresenter
{
	/**
	 * @var IHomepageGridFactory
	 */
	private $homepageGridFactory;
	/**
	 * @var IHomepageFormFactory
	 */
	private $homepageFormFactory;
	/**
	 * @var HomepageManager
	 */
	private $homepageManager;
	/**
	 * @var Homepage|NULL
	 */
	private $homepage = NULL;
	/**
	 * @var IDashboardControlFactory
	 */
	private $dashboardControlFactory;

	/**
	 * HomepagePresenter constructor.
	 * @param IHomepageGridFactory     $homepageGridFactory
	 * @param IHomepageFormFactory     $homepageFormFactory
	 * @param HomepageManager          $homepageManager
	 * @param IDashboardControlFactory $dashboardControlFactory
	 */
	public function __construct(IHomepageGridFactory $homepageGridFactory, IHomepageFormFactory $homepageFormFactory,
	                            HomepageManager $homepageManager, IDashboardControlFactory $dashboardControlFactory)
	{
		parent::__construct();
		$this->homepageGridFactory = $homepageGridFactory;
		$this->homepageFormFactory = $homepageFormFactory;
		$this->homepageManager = $homepageManager;
		$this->dashboardControlFactory = $dashboardControlFactory;
	}

	public function actionEdit(int $id = NULL)
	{
		if (!empty($id))
			$this->homepage = $this->homepageManager->getHomepageBlock($id);
	}

	public function renderDefault()
	{
		$this->template->anyVariable = 'any value';
	}

	public function renderList()
	{

	}

	public function renderEdit(int $id = NULL)
	{

	}

	/**
	 * @return \AdminModule\Components\HomepageGrid
	 */
	protected function createComponentHomepageGrid(): HomepageGrid
	{
		return $this->homepageGridFactory->create();
	}

	/**
	 * @return \AdminModule\Components\HomepageForm
	 */
	protected function createComponentHomepageForm(): HomepageForm
	{
		$homepageForm = $this->homepageFormFactory->create();
		$homepageForm->setHomepageBlock($this->homepage);
		return $homepageForm;
	}

	/**
	 * @return \AdminModule\Components\DashboardControl
	 */
	protected function createComponentDashboardControl(): DashboardControl
	{
		return $this->dashboardControlFactory->create();
	}



}
