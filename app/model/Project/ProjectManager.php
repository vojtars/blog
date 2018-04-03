<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Model;

use Kdyby\Doctrine\QueryBuilder;

class ProjectManager
{
	/**
	 * @var ProjectRepository
	 */
	private $projectRepository;

	/**
	 * ProjectManager constructor.
	 * @param ProjectRepository $projectRepository
	 */
	public function __construct(ProjectRepository $projectRepository)
	{
		$this->projectRepository = $projectRepository;
	}

	/**
	 * @return array|mixed
	 */
	public function getActiveProjects()
	{
		return $this->projectRepository->getActiveProjects();
	}

	/**
	 * @return \Kdyby\Doctrine\QueryBuilder
	 */
	public function getDataGridQuery(): QueryBuilder
	{
		return $this->projectRepository->getDataGridQuery();
	}

	public function getProject(int $projectId): Project
	{
		return $this->projectRepository->getProject($projectId);

	}
}