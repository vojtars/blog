<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */


namespace FrontModule\Presenters;

use Tracy\Debugger;
use Vojtars\Model\CategoryRepository;
use Vojtars\Model\PostRepository;
use Vojtars\Services\AppService;

class SearchPresenter extends BasePresenter
{
	/**
	 * @var PostRepository
	 */
	private $postRepository;


	/**
	 * HomepagePresenter constructor.
	 * @param PostRepository $postRepository
	 */
	public function __construct(PostRepository $postRepository)
	{
		parent::__construct();
		$this->postRepository = $postRepository;
	}

	public function renderDefault($url)
	{
		$this->handleSearch($url);
	}

}



 