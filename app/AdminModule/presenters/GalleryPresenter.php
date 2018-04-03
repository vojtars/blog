<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace AdminModule\Presenters;


use AdminModule\Components\GalleryForm;
use AdminModule\Components\GalleryGrid;
use AdminModule\Components\IGalleryFormFactory;
use AdminModule\Components\IGalleryGridFactory;
use Tracy\Debugger;
use Vojtars\Model\Gallery;
use Vojtars\Model\GalleryRepository;

class GalleryPresenter extends BasePresenter
{
    /**
     * @var IGalleryGridFactory
     */
    private $galleryGridFactory;

    /**
     * @var IGalleryFormFactory
     */
    private $galleryFormFactory;

    /**
     * @var Gallery
     */
    private $gallery;

    /**
     * GalleryPresenter constructor.
     * @param IGalleryGridFactory $galleryGridFactory
     * @param IGalleryFormFactory $galleryFormFactory
     * @param GalleryRepository $galleryRepository
     */
    public function __construct(IGalleryGridFactory $galleryGridFactory, IGalleryFormFactory $galleryFormFactory, GalleryRepository $galleryRepository )
    {
        parent::__construct();
        $this->galleryGridFactory = $galleryGridFactory;
        $this->galleryFormFactory = $galleryFormFactory;
        $this->galleryRepository = $galleryRepository;
    }

    public function renderDefault()
    {
        $this->template->anyVariable = 'any value';
    }

    public function actionDetail(int $galleryId)
    {
        $this->gallery = $this->galleryRepository->find($galleryId);
    }

	/**
	 * @return GalleryGrid
	 */
	public function createComponentGalleryGrid(): GalleryGrid
    {
        $control = $this->galleryGridFactory->create();
        $control->setUserEntity($this->userEntity);
        return $control;
    }

	/**
	 * @return GalleryForm
	 */
	public function createComponentGalleryForm() : GalleryForm
    {
        $control = $this->galleryFormFactory->create();
        $control->setGallery($this->gallery);
        $control->setUserEntity($this->userEntity);
        return $control;
    }

}