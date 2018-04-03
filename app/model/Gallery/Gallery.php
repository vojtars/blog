<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Model;

use Doctrine\ORM\Mapping as ORM;
use Kdyby\Doctrine\Entities\Attributes\Identifier;

/**
 * Gallery
 *
 * @ORM\Table(name="gallery")
 * @ORM\Entity()
 */
class Gallery
{

	Use Identifier;
	Use EntityValidator;

	const DEFAULT_GALLERY = 1;

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
	 * @var Image
	 *
	 * @ORM\ManyToOne(targetEntity="Image")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="image_id", referencedColumnName="id")
	 * })
	 */
	private $image;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="date_add", type="datetime", nullable=true)
	 */
	private $dateAdd;

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
	 * @var boolean
	 *
	 * @ORM\Column(name="active", type="boolean", nullable=true)
	 */
	private $active;

	/**
	 * @var \Vojtars\Model\Image[]|\Doctrine\Common\Collections\ArrayCollection
	 *
	 * @ORM\OneToMany(targetEntity="\Vojtars\Model\Gallery", mappedBy="gallery")
	 */
	private $images;

	/**
	 * Image constructor.
	 * @param string $name
	 */
	public function __construct($name)
	{
		$this->name = $name;
		$this->active = TRUE;
		$this->dateAdd = new \DateTime();
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name)
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return $this->description;
	}

	/**
	 * @return \DateTime
	 */
	public function getDateAdd(): \DateTime
	{
		return $this->dateAdd;
	}

	/**
	 * @return bool
	 */
	public function isActive(): bool
	{
		return $this->active;
	}

	/**
	 * @return Image|null
	 */
	public function getImage(): ?Image
	{
		return $this->checkImage($this->image);
	}

	/**
	 * @return Blog|null
	 */
	public function getBlog(): ?Blog
	{
		return $this->blog;
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection|\Vojtars\Model\Image[]
	 */
	public function getImages()
	{
		return $this->images;
	}

	/**
	 * @param Blog $blog
	 */
	public function setBlog(Blog $blog)
	{
		$this->blog = $blog;
	}

	/**
	 * @param string $description
	 */
	public function setDescription(string $description)
	{
		$this->description = $description;
	}

	/**
	 * @param Image $image
	 */
	public function setImage(Image $image)
	{
		$this->image = $image;
	}


}
