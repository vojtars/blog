<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace AdminModule\Presenters;

use Nette\Application\UI\Form;
use Nette\Http\FileUpload;
use Vojtars\Model\Blog;
use Vojtars\Model\BlogRepository;
use Vojtars\Model\GalleryRepository;
use Vojtars\Model\ImageManager;
use Vojtars\Model\SettingsRepository;
use Vojtars\Model\User;
use Vojtars\Model\UserRepository;

class BasePresenter extends \Presenters\BasePresenter
{
	/**
	 * @var Blog
	 */
	protected $blog;

	/**
	 * @inject
	 * @var BlogRepository
	 */
	public $blogRepository;
	
	/**
	 * @inject
	 * @var UserRepository
	 */
	public $userRepository;
	
	/**
	 * @inject
	 * @var GalleryRepository
	 */
	public $galleryRepository;

	/**
	 * @inject
	 * @var SettingsRepository
	 */
	public $settingsRepository;
	
	/**
	 * @inject
	 * @var ImageManager
	 */
	public $imageManager;
	
	/**
	 * @var User
	 */
	public $userEntity;

	/**
	 * @throws \Nette\Application\AbortException
	 * @throws \Nette\InvalidStateException
	 */
	public function startup()
	{
		parent::startup();

		if ((!$this->getUser()->isLoggedIn() && (!$this->getUser()->isInRole('admin')))) {
			$this->redirect(':Front:Sign:in', 'admin');
		}

		$this->userEntity = $this->userRepository->find($this->getUser()->getId());
	}

	public function beforeRender()
	{
		$this->template->blogs = $this->blogRepository->getActiveBlogs();
		$this->template->settings = $this->settingsRepository->getSettings();
	}

	public function handleUploadImage()
	{
		try {
			if (empty($this->blog))
				$gallery = $this->galleryRepository->getDefaultGallery();
			else
				$gallery = $this->galleryRepository->getBlogGallery($this->blog);

			/** @var FileUpload $file */
			$file = $this->getHttpRequest()->getFile('image');
			/** @var \Vojtars\Model\Image $image */
			$image = $this->imageManager->saveImage($file, $this->userEntity, $gallery);
		} catch (\Exception $exception) {
			$this->flashMessage('Obrázek se nepodařilo nahrát', 'danger');
			$this->redrawControl('flashes');
		}

		if (!empty($image)) {
			$url = '/img/upload/gallery/' . $gallery->getId() . '/' . $image->getName();
			$this->payload->url = $url;
			$this->sendPayload();
		} else {
			$this->flashMessage('Obrázek se nepodařilo nahrát', 'danger');
			$this->redrawControl('flashes');
		}
	}

}
