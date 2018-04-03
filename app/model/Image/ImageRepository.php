<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Model;

use Doctrine\ORM\QueryBuilder;
use Kdyby\Doctrine\EntityManager;

class ImageRepository extends BaseRepository
{
	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * CategoryRepository constructor.
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		parent::__construct($entityManager->getRepository(Image::class));
		$this->entityManager = $entityManager;
	}

	/**
	 * @param Gallery $gallery
	 * @return QueryBuilder
	 */
	public function getImagesForGrid(Gallery $gallery): QueryBuilder
	{
		return $this->createQueryBuilder('i')
			->where('i.gallery = :gallery')->setParameter('gallery', $gallery);
	}

	/**
	 * @param Gallery $gallery
	 * @return array|Image[]
	 */
	public function getActiveGalleryImages(Gallery $gallery): array
	{
		return $this->createQueryBuilder('i')
			->where('i.gallery = :gallery')->setParameter('gallery', $gallery)
			->andWhere('i.active = :active')->setParameter('active', TRUE)
			->getQuery()->getResult();
	}
}