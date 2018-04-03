<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Model;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;

/**
 * Post
 *
 * @ORM\Table(name="post")
 * @ORM\Entity()
 */
class Post
{

	Use Identifier;
	Use EntityValidator;

	/**
	 * @var Blog
	 *
	 * @ORM\ManyToOne(targetEntity="Blog")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="blog_id", referencedColumnName="id")
	 * })
	 */
	private $blog;

	/**
	 * @var Category
	 *
	 * @ORM\ManyToOne(targetEntity="Category")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="category_id", referencedColumnName="id")
	 * })
	 */
	private $category;

	/**
	 * @var Gallery
	 *
	 * @ORM\ManyToOne(targetEntity="Gallery")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="gallery_id", referencedColumnName="id")
	 * })
	 */
	private $gallery;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", nullable=true)
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="description", type="string", nullable=true)
	 */
	private $description;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="url", type="string", nullable=true)
	 */
	private $url;

	/**
	 * @var Image
	 *
	 * @ORM\ManyToOne(targetEntity="Image")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="image_id", referencedColumnName="id")
	 * })
	 */
	private $image;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="content", type="string", nullable=true)
	 */
	private $content;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="view_count", type="integer", nullable=true)
	 */
	private $viewCount;

	/**
	 * @var DateTime
	 *
	 * @ORM\Column(name="date_add", type="datetime", nullable=true)
	 */
	private $dateAdd;

	/**
	 * @var User
	 *
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="add_user_id", referencedColumnName="id")
	 * })
	 */
	private $addUser;

	/**
	 * @var DateTime
	 *
	 * @ORM\Column(name="date_edit", type="datetime", nullable=true)
	 */
	private $dateEdit;

	/**
	 * @var User
	 *
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="last_edit_user_id", referencedColumnName="id")
	 * })
	 */
	private $lastEditUser;

	/**
	 * @var DateTime
	 *
	 * @ORM\Column(name="last_view_date", type="datetime", nullable=true)
	 */
	private $lastViewDate;

	/**
	 * @var DateTime
	 *
	 * @ORM\Column(name="public_date", type="datetime", nullable=true)
	 */
	private $publicDate;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="public", type="boolean", nullable=true)
	 */
	private $public;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="deleted", type="boolean", nullable=true)
	 */
	private $deleted;

	/**
	 * @var \Vojtars\Model\PostHasFile[]|\Doctrine\Common\Collections\ArrayCollection
	 *
	 * @ORM\OneToMany(targetEntity="\Vojtars\Model\PostHasFile", mappedBy="post")
	 */
	private $postHasFiles;


	/**
	 * Post constructor.
	 * @param Category $category
	 * @param string   $name
	 * @param User     $addUser
	 * @param Blog     $blog
	 */
	public function __construct(Category $category = NULL, $name, User $addUser, Blog $blog)
	{
		$this->category = $category;
		$this->name = $name;
		$this->addUser = $addUser;
		$this->deleted = FALSE;
		$this->dateAdd = new DateTime();
		$this->dateEdit = new DateTime();
		$this->lastEditUser = $addUser;
		$this->viewCount = 0;
		$this->blog = $blog;
	}

	/**
	 * @return Category|null
	 */
	public function getCategory(): ?Category
	{
		return $this->category;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function getUrl(): string
	{
		return $this->url;
	}

	/**
	 * @return Gallery|NULL
	 */
	public function getGallery(): ?Gallery
	{
		return $this->gallery;
	}

	/**
	 * @return Image|null
	 */
	public function getImage(): ?Image
	{
		return $this->checkImage($this->image);
	}

	/**
	 * @return string
	 */
	public function getContent(): string
	{
		return $this->content;
	}

	/**
	 * @return int
	 */
	public function getViewCount(): int
	{
		return $this->viewCount;
	}

	/**
	 * @return \DateTime
	 */
	public function getDateAdd(): \DateTime
	{
		return $this->dateAdd;
	}

	/**
	 * @return User
	 */
	public function getAddUser(): User
	{
		return $this->addUser;
	}

	/**
	 * @return \DateTime
	 */
	public function getDateEdit(): \DateTime
	{
		return $this->dateEdit;
	}

	/**
	 * @return User
	 */
	public function getLastEditUser(): User
	{
		return $this->lastEditUser;
	}

	/**
	 * @return \DateTime
	 */
	public function getLastViewDate(): \DateTime
	{
		return $this->lastViewDate;
	}


	/**
	 * @return \DateTime
	 */
	public function getPublicDate(): \DateTime
	{
		return $this->publicDate;
	}

	/**
	 * @return bool
	 */
	public function isPublic(): bool
	{
		return $this->public;
	}

	/**
	 * @return bool
	 */
	public function isDeleted(): bool
	{
		return $this->deleted;
	}

	/**
	 * @return Blog
	 */
	public function getBlog(): Blog
	{
		return $this->blog;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name)
	{
		$this->name = $name;
	}

	/**
	 * @param Category $category
	 */
	public function setCategory(Category $category)
	{
		$this->category = $category;
	}

	/**
	 * @param string $description
	 */
	public function setDescription(string $description)
	{
		$this->description = $description;
	}

	/**
	 * @param string $url
	 */
	public function setUrl(string $url)
	{
		$this->url = $url;
	}

	/**
	 * @param Image $image
	 */
	public function setImage(Image $image)
	{
		$this->image = $image;
	}

	/**
	 * @param string $content
	 */
	public function setContent(string $content)
	{
		$this->content = $content;
	}

	/**
	 * @param DateTime $dateEdit
	 */
	public function setDateEdit(DateTime $dateEdit)
	{
		$this->dateEdit = $dateEdit;
	}

	/**
	 * @param User $lastEditUser
	 */
	public function setLastEditUser(User $lastEditUser)
	{
		$this->lastEditUser = $lastEditUser;
	}

	/**
	 * @param DateTime $lastViewDate
	 */
	public function setLastViewDate(DateTime $lastViewDate)
	{
		$this->lastViewDate = $lastViewDate;
	}

	/**
	 * @param DateTime $publicDate
	 */
	public function setPublicDate(DateTime $publicDate)
	{
		$this->publicDate = $publicDate;
	}

	/**
	 * @param bool $public
	 */
	public function setPublic(bool $public)
	{
		$this->public = $public;
	}

	/**
	 * @param bool $deleted
	 */
	public function setDeleted(bool $deleted)
	{
		$this->deleted = $deleted;
	}

	public function addView()
	{
		$this->viewCount++;
		$this->lastViewDate = new DateTime();
	}

	/**
	 * @param Gallery|NULL $gallery
	 */
	public function setGallery(?Gallery $gallery): void
	{
		$this->gallery = $gallery;
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection|PostHasFile[]
	 */
	public function getPostHasFiles()
	{
		return $this->postHasFiles;
	}



}
