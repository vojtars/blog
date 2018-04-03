<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Model;

class GalleryManager
{
	/**
	 * @var GalleryRepository
	 */
	private $galleryRepository;

	/**
	 * GalleryManager constructor.
	 * @param GalleryRepository $galleryRepository
	 */
	public function __construct(GalleryRepository $galleryRepository)
	{
		$this->galleryRepository = $galleryRepository;
	}

	/**
	 * @return array
	 */
	public function getGalleriesForSelect(): array
	{
		$galleriesArray[0] = 'Bez galerie';
		$galleries = $this->galleryRepository->getActiveGalleries();
		/** @var Gallery $gallery */
		foreach ($galleries as $gallery) {
			$galleriesArray[$gallery->getId()] = $gallery->getName();
		}
		return $galleriesArray;
	}

	/**
	 * @param int $id
	 * @return Gallery
	 * @throws NoGalleryException
	 */
	public function getGallery(int $id): Gallery
	{
		return $this->galleryRepository->getGallery($id);
	}
}