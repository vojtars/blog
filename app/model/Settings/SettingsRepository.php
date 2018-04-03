<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Model;

use Kdyby\Doctrine\EntityManager;
use Kdyby\Doctrine\QueryBuilder;

class SettingsRepository extends BaseRepository
{

	CONST SETTINGS_ID = 1;

	/**
	 * SettingsRepository constructor.
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		parent::__construct($entityManager->getRepository(Settings::class));
	}

	/**
	 * @return Settings
	 */
	public function getSettings(): Settings
	{
		/** @var Settings $settings */
		$settings = $this->find(self::SETTINGS_ID);
		return $this->checkResult($settings, new NoSettingsException('Settings not found.'));
	}

	public function getDataGridQuery(): QueryBuilder
	{
		return $this->createQueryBuilder('s');
	}
}