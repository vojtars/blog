<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Model;

use Doctrine\ORM\QueryBuilder;

class SubscriberManager
{
	/**
	 * @var SubscriberRepository
	 */
	private $subscriberRepository;


	/**
	 * SubscriberManager constructor.
	 * @param SubscriberRepository $subscriberRepository
	 */
	public function __construct(SubscriberRepository $subscriberRepository)
	{
		$this->subscriberRepository = $subscriberRepository;
	}

	/**
	 * @return QueryBuilder
	 */
	public function getDataGridQuery(): QueryBuilder
	{
		return $this->subscriberRepository->getDataGridQuery();
	}

	/**
	 * @param int $id
	 * @return Subscriber
	 * @throws SubscriberException
	 */
	public function getSubscriber(int $id): Subscriber
	{
		return $this->subscriberRepository->getSubscriber($id);
	}
}