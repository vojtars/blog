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

class FileRepository extends BaseRepository
{

	/**
	 * FileRepository constructor.
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		parent::__construct($entityManager->getRepository(File::class));
	}

	/**
	 * @return QueryBuilder
	 */
	public function getFilersForGrid(): QueryBuilder
	{
		return $this->createQueryBuilder('f');
	}

	/**
	 * @param int $id
	 * @return File
	 * @throws
	 */
	public function getFile(int $id): File
	{
		$file = $this->find($id);
		return $this->checkResult($file, new FileException('File not found'));
	}

	/**
	 * @param string $fileName
	 * @return File
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 * @throws FileException
	 */
	public function getFileByName(string $fileName): File
	{
		$file = $this->createQueryBuilder('f')
			->where('f.fileName = :fileName')->setParameter('fileName', $fileName)
			->getQuery()->getOneOrNullResult();

		return $this->checkResult($file, new FileException('File with this fileName not found.'));
	}
}