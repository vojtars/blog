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
use Vojtars\Model\HomepageRepository;
use Vojtars\Model\PostRepository;
use Vojtars\Services\AppService;

class HomepagePresenter extends BasePresenter
{
	/**
	 * @var PostRepository
	 */
	private $postRepository;
	/**
	 * @var HomepageRepository
	 */
	private $homepageRepository;


	/**
	 * HomepagePresenter constructor.
	 * @param PostRepository     $postRepository
	 * @param HomepageRepository $homepageRepository
	 */
	public function __construct(PostRepository $postRepository, HomepageRepository $homepageRepository)
	{
		parent::__construct();
		$this->postRepository = $postRepository;
		$this->homepageRepository = $homepageRepository;
	}

	/**
	 * @throws \Nette\Application\UI\InvalidLinkException
	 */
	public function actionDefault()
	{
		$this->editLink = $this->link(':Admin:Homepage:list');
		$this->template->posts = $this->postRepository->getLastPosts(3);
		$this->template->blocks = $this->homepageRepository->getActiveBlocks();
	}


}
