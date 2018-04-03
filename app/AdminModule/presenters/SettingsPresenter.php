<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace AdminModule\Presenters;


use AdminModule\Components\ContactControl;
use AdminModule\Components\IAboutMeFormFactory;
use AdminModule\Components\IContactControlFactory;
use AdminModule\Components\IMarketingControlFactory;
use AdminModule\Components\ISettingsControlFactory;
use AdminModule\Components\ISubscriberGridFactory;
use AdminModule\Components\ITermsFormFactory;
use AdminModule\Components\MarketingControl;
use AdminModule\Components\SettingsControl;
use AdminModule\Components\SubscriberGrid;

class SettingsPresenter extends BasePresenter
{
	/**
	 * @var IAboutMeFormFactory
	 */
	private $aboutMeFormFactory;
	/**
	 * @var ISettingsControlFactory
	 */
	private $settingsControlFactory;
	/**
	 * @var IMarketingControlFactory
	 */
	private $marketingControlFactory;
	/**
	 * @var IContactControlFactory
	 */
	private $contactControlFactory;
	/**
	 * @var ISubscriberGridFactory
	 */
	private $subscriberGridFactory;
	/**
	 * @var ITermsFormFactory
	 */
	private $termsFormFactory;


	/**
	 * SettingsPresenter constructor.
	 * @param IAboutMeFormFactory      $aboutMeFormFactory
	 * @param ISettingsControlFactory  $settingsControlFactory
	 * @param IMarketingControlFactory $marketingControlFactory
	 * @param IContactControlFactory   $contactControlFactory
	 * @param ISubscriberGridFactory   $subscriberGridFactory
	 * @param ITermsFormFactory        $termsFormFactory
	 */
	public function __construct(IAboutMeFormFactory $aboutMeFormFactory, ISettingsControlFactory $settingsControlFactory,
	                            IMarketingControlFactory $marketingControlFactory, IContactControlFactory $contactControlFactory,
	                            ISubscriberGridFactory $subscriberGridFactory, ITermsFormFactory $termsFormFactory)
	{
		parent::__construct();
		$this->aboutMeFormFactory = $aboutMeFormFactory;
		$this->settingsControlFactory = $settingsControlFactory;
		$this->marketingControlFactory = $marketingControlFactory;
		$this->contactControlFactory = $contactControlFactory;
		$this->subscriberGridFactory = $subscriberGridFactory;
		$this->termsFormFactory = $termsFormFactory;
	}

	/**
	 * @return \AdminModule\Components\AboutMeForm
	 */
	protected function createComponentAboutMeForm()
	{
		$aboutMeForm = $this->aboutMeFormFactory->create();
		$aboutMeForm->setUserEntity($this->userEntity);
		$aboutMeForm->setSettings($this->settingsRepository->getSettings());
		return $aboutMeForm;
	}

	/**
	 * @return \AdminModule\Components\SettingsControl
	 */
	protected function createComponentSettingsControl(): SettingsControl
	{
		$control = $this->settingsControlFactory->create();
		$control->setUser($this->userEntity);
		return $control;
	}

	/**
	 * @return \AdminModule\Components\MarketingControl
	 */
	protected function createComponentMarketingControl(): MarketingControl
	{
		$control = $this->marketingControlFactory->create();
		$control->setUser($this->userEntity);
		return $control;
	}

	/**
	 * @return ContactControl
	 */
	protected function createComponentContactsControl(): ContactControl
	{
		$control = $this->contactControlFactory->create();
		$control->setUser($this->userEntity);
		return $control;
	}

	/**
	 * @return SubscriberGrid
	 */
	protected function createComponentSubscriberGrid(): SubscriberGrid
	{
		$control = $this->subscriberGridFactory->create();
		return $control;
	}

	protected function createComponentTermsForm()
	{
		$control = $this->termsFormFactory->create();
		return $control;
	}
}


 