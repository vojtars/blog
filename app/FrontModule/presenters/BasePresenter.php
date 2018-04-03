<?php declare(strict_types=1);

namespace FrontModule\Presenters;

use FrontModule\Components\AdminControl;
use FrontModule\Components\IAdminControlFactory;
use FrontModule\Components\ISearchFormFactory;
use FrontModule\Components\ISubscriberFormFactory;
use FrontModule\Components\SearchForm;
use FrontModule\Components\SubscriberForm;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Form;
use Nette\Security\Passwords;
use Tracy\Debugger;
use Vojtars\Model\Blog;
use Vojtars\Model\BlogRepository;
use Vojtars\Model\Category;
use Vojtars\Model\PostManager;
use Vojtars\Model\Settings;
use Vojtars\Model\SettingsRepository;

class BasePresenter extends \Presenters\BasePresenter
{

	/**
	 * @var EntityManager
	 * @inject
	 */
	public $entityManager;

	/**
	 * @inject
	 * @var PostManager
	 */
	public $postManager;
	
	/**
	 * @inject
	 * @var BlogRepository
	 */
	public $blogRepository;
	
	/**
	 * @inject
	 * @var SettingsRepository
	 */
	public $settingsRepository;

	/**
	 * @inject
	 * @var ISubscriberFormFactory
	 */
	public $subscriberFormFactory;

	/**
	 * @inject
	 * @var ISearchFormFactory
	 */
	public $searchFormFactory;

	/**
	 * @inject
	 * @var IAdminControlFactory
	 */
	public $adminControlFactory;
	
	/**
	 * @var Settings
	 */
	protected $settings;

	/**
	 * @var Blog
	 */
	protected $blog;

	/**
	 * @var Category
	 */
	protected $category;

	/**
	 * @var string
	 */
	protected $editLink = NULL;


	public function startup()
	{
		parent::startup();

	}

	public function beforeRender()
	{
		$this->template->settings = $this->settings = $this->settingsRepository->getSettings();
		$this->template->blogs = $this->blogRepository->getActiveBlogs();
		$this->initHead();
	}

	/**
	 * @param string|NULL $searchQuery
	 */
	public function handleSearch(string $searchQuery = NULL)
	{
		if (!empty($this->category)) {
			$query = urldecode($searchQuery);
			$this->template->posts = $this->postManager->searchInCategoryPosts($this->category, $query);
			$this->postGet('this');
			$this->redrawControl('posts');
		}

		if (!empty($this->blog)) {
			$query = urldecode($searchQuery);
			$this->template->posts = $this->postManager->searchInBlogPosts($this->blog, $query);
			$this->postGet('this');
			$this->redrawControl('posts');
		}

		$this->template->query = $searchQuery;
		$this->template->posts = $this->postManager->searchInAllPosts($searchQuery);
	}

	private function initHead()
	{
		$this->template->headDescription = $this->settings->getHeadDescription();
		$this->template->headKeywords = $this->settings->getHeadKeywords();
		$this->template->headUrl = $this->getHttpRequest()->getUrl()->getAbsoluteUrl();
		$this->template->headTitle = $this->settings->getHeadTitle();
		$this->template->headImage = $this->settings->getHeadImage();
	}

	/**
	 * @return SubscriberForm
	 */
	protected function createComponentSubscriberForm(): SubscriberForm
	{
		$subscriberForm = $this->subscriberFormFactory->create();
		$subscriberForm->onSubmit[] = function (string $message, bool $success) {
			if ($success)
				$this->flashMessage($message);
			else
				$this->flashMessage($message, 'danger');

			$this->postGet('this');
			$this->redrawControl();
		};
		return $subscriberForm;
	}

	/**
	 * @return SearchForm
	 */
	protected function createComponentSearchForm(): SearchForm
	{
		return $this->searchFormFactory->create();
	}

	protected function createComponentAdminControl(): AdminControl
	{
		$control = $this->adminControlFactory->create();
		$control->setLink($this->editLink);
		return $control;
	}



}
