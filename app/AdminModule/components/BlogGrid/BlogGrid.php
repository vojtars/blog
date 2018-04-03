<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace AdminModule\Components;

use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;
use Nette\Utils\Html;
use Nette\Utils\Random;
use Ublaboo\DataGrid\DataGrid;
use Vojtars\Model\Blog;
use Vojtars\Model\BlogRepository;
use Vojtars\Model\NoBlogException;
use Vojtars\Model\Post;

class BlogGrid extends Control
{
	use OwnTemplate;

	/**
	 * @var EntityManager
	 */
	private $entityManager;

	/**
	 * @var BlogRepository
	 */
	private $blogRepository;


	/**
	 * UserGrid constructor.
	 * @param EntityManager  $entityManager
	 * @param BlogRepository $blogRepository
	 */
	public function __construct(EntityManager $entityManager, BlogRepository $blogRepository)
	{
		parent::__construct();
		$this->entityManager = $entityManager;
		$this->setTemplateName('blogGrid.latte');
		$this->blogRepository = $blogRepository;
	}

	/**
	 * @throws \ReflectionException
	 */
	public function render()
	{
		$template = $this->getTemplate();
		$template->setFile($this->getTemplateFullPath());
		$template->render();
	}

	/**
	 * @return DataGrid
	 * @throws \Ublaboo\DataGrid\Exception\DataGridColumnStatusException
	 * @throws \Ublaboo\DataGrid\Exception\DataGridException
	 */
	public function createComponentBlogGrid()
	{
		$grid = new DataGrid();

		$grid->setDataSource($this->blogRepository->getDataGridQuery());
		$grid->setDefaultPerPage(20);
		$grid->setItemsPerPageList([20, 50, 100, 200]);
		$grid->setRememberState(FALSE);

		$grid->addColumnText('id', 'Id')
			->setSortable();

		$grid->addColumnText('image', 'Náhled')
			->setSortable()
			->setRenderer(function (Blog $blog) {
				if (empty($blog->getImage())) {
					return NULL;
				} else {
					return Html::el('img')
						->setAttribute('style', 'max-width: 100px; max-height: 100px;')
						->setAttribute('src', '/' . $blog->getImage()->getMiniNameWithPath())
						->setAttribute('alt', $blog->getImage()->getDescription());
				}
			});

		$grid->addColumnText('name', 'Název')
			->setSortable()
			->setEditableCallback(function ($id, $value) {
				/** @var Blog $blog */
				$blog = $this->blogRepository->find((int)$id);
				$blog->setName($value);
				$this->entityManager->flush($blog);
			});

		$grid->addColumnText('url', 'URL')
			->setSortable()
			->setEditableCallback(function ($id, $value) {
				/** @var Blog $blog */
				$blog = $this->blogRepository->find((int)$id);
				if ($value != $blog->getUrl())
					$blog->setUrl($this->checkBlogtUrl($value));

				$this->entityManager->flush($blog);
			});

		$grid->addColumnText('dateAdd', 'Vytvořeno')
			->setSortable()
			->setRenderer(function (Blog $blog) {
				if (empty($blog->getDateAdd())) {
					return '---';
				} else {
					return $blog->getDateAdd()->format('d.m.Y H:i:s');
				}
			});

		$grid->addColumnStatus('active', 'Aktivní')
			->setSortable()
			->addOption(1, 'Aktivní')
			->setIcon('check')
			->setClass('btn-success')
			->endOption()
			->addOption(0, 'Neaktivní')
			->setIcon('close')
			->setClass('btn-danger')
			->endOption()
			->onChange[] = function ($id, $newValue) {

			/** @var Blog $blog */
			$blog = $this->blogRepository->find((int)$id);
			$blog->setActive((bool)$newValue);
			$this->entityManager->flush($blog);

			if ($this->getPresenter()->isAjax()) {
				$this['blogGrid']->reload();
			} else {
				$this->redirect('this');
			}
		};

		$grid->addAction('edit', 'Upravit', 'edit!')
			->setIcon('edit')
			->setTitle('Upravit')
			->setClass('btn btn-xs btn-success');

		return $grid;
	}

	/**
	 * @param int $id
	 * @throws \Nette\Application\AbortException
	 */
	public function handleEdit(int $id)
	{
		$this->getPresenter()->redirect('Blog:detail', $id);
	}


	/**
	 * @param string $url
	 * @return string
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 * @throws \Vojtars\Model\NoBlogException
	 */
	private function checkBlogtUrl(string $url)
	{
		try {
			$this->blogRepository->getBlogByUrl($url);
			return $this->checkBlogtUrl($url.'-'.Random::generate(1, 'a-z'));
		} catch (NoBlogException $noBlogException) {
			return $url;
		}
	}


}