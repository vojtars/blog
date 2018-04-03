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

class ProjectRepository extends BaseRepository
{

	/**
	 * ProjectRepository constructor.
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		parent::__construct($entityManager->getRepository(Project::class));
	}

	/**
	 * @return array|mixed
	 */
	public function getActiveProjects()
	{
		return $this->findBy(['active' => TRUE],['position' => 'ASC']);
	}

	/**
	 * @return QueryBuilder
	 */
	public function getDataGridQuery(): QueryBuilder
	{
		return $this->createQueryBuilder('p');
	}

	/**
	 * @param int $projectId
	 * @return Project
	 */
	public function getProject(int $projectId): Project
	{
		$project = $this->find($projectId);
		return $this->checkResult($project, new NoProjectException('Project not found'));
	}

}