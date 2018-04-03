<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Model;

use Kdyby\Doctrine\QueryBuilder;

class HomepageManager
{
	/**
	 * @var HomepageRepository
	 */
	private $homepageRepository;

	/**
	 * HomepageManager constructor.
	 * @param HomepageRepository $homepageRepository
	 */
	public function __construct(HomepageRepository $homepageRepository)
	{
		$this->homepageRepository = $homepageRepository;
	}

	/**
	 * @return QueryBuilder
	 */
	public function getDataGridQuery(): QueryBuilder
	{
		return $this->homepageRepository->getDataGridQuery();
	}

	/**
	 * @param int $id
	 * @return Homepage
	 */
	public function getHomepageBlock(int $id): Homepage
	{
		return $this->homepageRepository->getHomepageBlock($id);
	}
}