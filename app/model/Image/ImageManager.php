<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Model;

use Doctrine\ORM\EntityManager;
use Nette\Http\FileUpload;
use Nette\Utils\Random;
use Tracy\Debugger;

class ImageManager
{
	/**
	 * @var EntityManager
	 */
	private $entityManager;
	/**
	 * @var ImageRepository
	 */
	private $imageRepository;

	/**
	 * ImageManager constructor.
	 * @param EntityManager   $entityManager
	 * @param ImageRepository $imageRepository
	 */
	public function __construct(EntityManager $entityManager, ImageRepository $imageRepository)
	{
		$this->entityManager = $entityManager;
		$this->imageRepository = $imageRepository;
	}

	/**
	 * @param FileUpload  $file
	 * @param User        $user
	 * @param Gallery     $gallery
	 * @param string|NULL $description
	 * @return null|Image
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\ORMInvalidArgumentException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 * @throws \Nette\InvalidArgumentException
	 * @throws \Nette\NotSupportedException
	 * @throws \Nette\Utils\UnknownImageFileException
	 */
	public function saveImage(FileUpload $file, User $user, Gallery $gallery, string $description = NULL): ?Image
	{
		$file_name = NULL;
		if (!empty($file)) {
			if ($file->isImage() and $file->isOk()) {
				// oddělení přípony pro účel změnit název souboru na co chceš se zachováním přípony
				$file_ext = strtolower(mb_substr($file->getSanitizedName(), strrpos($file->getSanitizedName(), ".")));
				// vygenerování náhodného řetězce znaků, můžeš použít i \Nette\Strings::random()
				$file_name_x = uniqid(Random::generate(5), TRUE);
				$file_name = $file_name_x . $file_ext;
				// přesunutí souboru z temp složky někam, kam nahráváš soubory
				$file->move(IMG_UPLOAD_TEMP_DIR . $file_name);

				//v případě, že chceš vytvořit z obrázku i miniaturu
				$image = \Nette\Utils\Image::fromFile(IMG_UPLOAD_TEMP_DIR . $file_name);

				$imageSmall = clone $image;

				if ($image->getWidth() > $image->getHeight()) {

					if ($image->getWidth() > 1080) {
						$image->resize(1080, NULL);
					}
					$imageSmall->resize(640, NULL);
				} else {

					if ($image->getHeight() > 1080) {
						$image->resize(NULL, 1080);
					}
					$imageSmall->resize(NULL, 640);
				}

				$image->sharpen();

				$imageDir = IMG_GALLERY_DIR . $gallery->getId();
				if (!is_dir($imageDir)) {
					mkdir($imageDir, 0777, TRUE);
				}
				$image->save($imageDir . '/' . $file_name);

				$imageSmall->sharpen();
				$imageSmall->save($imageDir . '/' . $file_name_x . '-mini' . $file_ext);
				unlink(IMG_UPLOAD_TEMP_DIR . $file_name);

				$image = new Image($file_name, $gallery, $user);
				$image->setDescription($description);
				$this->entityManager->persist($image);
				$this->entityManager->flush($image);
				return $image;
			} else {
				return NULL;
			}
		} else {
			return NULL;
		}
	}

	/**
	 * @param Gallery $gallery
	 * @return array|Image[]
	 */
	public function getActiveGalleryImages(Gallery $gallery): array
	{
		return $this->imageRepository->getActiveGalleryImages($gallery);
	}

}