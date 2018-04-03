<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace AdminModule\Presenters;


use AdminModule\Components\FilesGrid;
use AdminModule\Components\IFilesGridFactory;

class FilePresenter extends BasePresenter
{
	/**
	 * @var IFilesGridFactory
	 */
	private $filesGridFactory;


	/**
	 * FilePresenter constructor.
	 * @param IFilesGridFactory $filesGridFactory
	 */
	public function __construct(IFilesGridFactory $filesGridFactory)
	{
		parent::__construct();
		$this->filesGridFactory = $filesGridFactory;
	}

	public function renderDefault()
	{
		$this->template->anyVariable = 'any value';
	}

	/**
	 * @return \AdminModule\Components\FilesGrid
	 */
	protected function createComponentFilesGrid(): FilesGrid
	{
		return $this->filesGridFactory->create();
	}
}