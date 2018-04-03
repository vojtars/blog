<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Model;

use Kdyby\Doctrine\QueryBuilder;

class FileManager
{
	/**
	 * @var FileRepository
	 */
	private $fileRepository;
	/**
	 * @var PostHasFileRepository
	 */
	private $postHasFileRepository;

	/**
	 * FileManager constructor.
	 * @param FileRepository        $fileRepository
	 * @param PostHasFileRepository $postHasFileRepository
	 */
	public function __construct(FileRepository $fileRepository, PostHasFileRepository $postHasFileRepository)
	{
		$this->fileRepository = $fileRepository;
		$this->postHasFileRepository = $postHasFileRepository;
	}

	/**
	 * @return QueryBuilder
	 */
	public function getFilesForGrid(): QueryBuilder
	{
		return $this->fileRepository->getFilersForGrid();
	}

	/**
	 * @param int $id
	 * @return File
	 */
	public function getFile(int $id): File
	{
		return $this->fileRepository->getFile($id);
	}

	/**
	 * @param string $fileName
	 * @return File
	 * @throws FileException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function getFileByName(string $fileName): File
	{
		return $this->fileRepository->getFileByName($fileName);
	}

	public function getFilesForSelect()
	{
		$filesArray = [];
		$files = $this->fileRepository->findAll();
		/** @var File $file */
		foreach ($files as $file) {
			$filesArray[$file->getId()] = empty($file->getName()) ? $file->getFileName() : $file->getName();
		}
		return $filesArray;
	}

	/**
	 * @param Post $post
	 */
	public function removeAllPostFiles(Post $post)
	{
		$this->postHasFileRepository->removeAllPostFiles($post);

	}

	/**
	 * @param Post $post
	 * @return array
	 */
	public function getPostFilesIds(Post $post): array
	{
		$ids = [];
		$postHasFiles = $this->postHasFileRepository->findPostFiles($post);
		/** @var PostHasFile $postHasFile */
		foreach ($postHasFiles as $postHasFile) {
			$ids[] = $postHasFile->getFile()->getId();
		}
		return $ids;
	}
}