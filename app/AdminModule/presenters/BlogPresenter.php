<?php declare(strict_types=1);
/**
 * Copyright (c) 2018. 
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace AdminModule\Presenters;

use AdminModule\Components\BlogForm;
use AdminModule\Components\BlogGrid;
use AdminModule\Components\IBlogFormFactory;
use AdminModule\Components\IBlogGridFactory;
use Nette;
use Tracy\Debugger;
use Vojtars\Model;


class BlogPresenter extends BasePresenter
{
    /**
     * @var IBlogGridFactory
     */
    private $blogGridFactory;
    
    /**
     * @var IBlogFormFactory
     */
    private $blogFormFactory;


    /**
     * BlogPresenter constructor.
     * @param IBlogGridFactory $blogGridFactory
     * @param IBlogFormFactory $blogFormFactory
     */
    public function __construct(IBlogGridFactory $blogGridFactory, IBlogFormFactory $blogFormFactory )
    {
    	parent::__construct();
        $this->blogGridFactory = $blogGridFactory;
        $this->blogFormFactory = $blogFormFactory;
    }

	/**
	 * @param int|null $id
	 * @throws Model\NoBlogException
	 */
	public function actionDetail(int $id = NULL)
	{
		$this->blog = empty($id) ? NULL : $this->blogRepository->getBlog($id);
    }

    public function renderDefault()
    {
        $this->template->anyVariable = 'any value';
    }

	/**
	 * @param int|NULL $id
	 */
	public function renderDetail(int $id = NULL)
    {

    }

	/**
	 * @return \AdminModule\Components\BlogGrid
	 */
	public function createComponentBlogGrid(): BlogGrid
    {
        return $this->blogGridFactory->create();
    }

	/**
	 * @return \AdminModule\Components\BlogForm
	 */
	public function createComponentBlogForm(): BlogForm
    {
        $control =  $this->blogFormFactory->create();
        $control->setBlog($this->blog);
        $control->setUserEntity($this->userEntity);
        return $control;
    }

}
