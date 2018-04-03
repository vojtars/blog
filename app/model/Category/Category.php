<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Model;

use Doctrine\ORM\Mapping AS ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette\Utils\Strings;

/**
 * Category
 *
 * @ORM\Table(name="category")
 * @ORM\Entity()
 *
 */
class Category
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
	 * @var \DateTime
	 *
	 * @ORM\Column(name="date_add", type="datetime", nullable=true)
	 */
	private $dateAdd;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="active", type="boolean", nullable=true)
	 */
	private $active;

	/**
	 * @var \Vojtars\Model\Post[]|\Doctrine\Common\Collections\ArrayCollection
	 *
	 * @ORM\OneToMany(targetEntity="\Vojtars\Model\Post", mappedBy="category")
	 */
	private $posts;

	/**
	 * Category constructor.
	 * @param string $name
	 * @param Blog   $blog
	 * @param null   $url
	 */
	public function __construct($name, Blog $blog, $url = NULL)
	{
		$this->name = $name;
		$this->blog = $blog;
		$this->url = empty($url) ? Strings::webalize($name) : Strings::webalize($url);
		$this->dateAdd = new \DateTime();
		$this->active = TRUE;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return null|string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->url;
	}

	/**
	 * @return \DateTime
	 */
	public function getDateAdd(): \DateTime
	{
		return $this->dateAdd;
	}

	/**
	 * @return Image|null
	 */
	public function getImage(): ?Image
	{
		return $this->checkImage($this->image);
	}

	/**
	 * @return bool
	 */
	public function isActive(): bool
	{
		return $this->active;
	}

	/**
	 * @return Blog
	 */
	public function getBlog(): Blog
	{
		return $this->blog;
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection|\Vojtars\Model\Post[]
	 */
	public function getPosts()
	{
		return $this->posts;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name)
	{
		$this->name = $name;
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
	 * @param bool $active
	 */
	public function setActive(bool $active)
	{
		$this->active = $active;
	}


}
