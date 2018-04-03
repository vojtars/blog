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

class GalleryRepository extends BaseRepository
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
		parent::__construct($entityManager->getRepository(Gallery::class));
		$this->entityManager = $entityManager;
	}

	/**
	 * @return QueryBuilder
	 */
	public function getDataGridQuery(): QueryBuilder
	{
		return $this->createQueryBuilder('g');
	}

	/**
	 * @param Blog $blog
	 * @return Gallery
	 * @throws \Exception
	 */
	public function getBlogGallery(Blog $blog): Gallery
	{
		$query = $this->createQueryBuilder('g')
			->where('g.blog = :blog')->setParameter('blog', $blog)
			->getQuery()->getOneOrNullResult();

		if (empty($query)) {
			throw new \Exception('Galerie nemá blog');
		} else {
			return $query;
		}
	}

	/**
	 * @return null|object
	 */
	public function getDefaultGallery()
	{
		return $this->find(Gallery::DEFAULT_GALLERY);
	}

	/**
	 * @return array|Gallery[]
	 */
	public function getActiveGalleries(): array
	{
		return $this->createQueryBuilder('g')
			->where('g.id > 1')
			->andWhere('g.blog IS NULL')
			->andWhere('g.active = :active')->setParameter('active', TRUE)
			->getQuery()->getResult();
	}

	/**
	 * @param int $id
	 * @return Gallery
	 * @throws NoGalleryException
	 */
	public function getGallery(int $id): Gallery
	{
		$gallery = $this->find($id);
		return $this->checkResult($gallery, new NoGalleryException('Gallery not found'));
	}
}