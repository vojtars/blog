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
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity()
 */
class Image
{

	Use Identifier;

	const GALLERY_PATH = "img/upload/gallery/";

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
	 * @var User
	 *
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 * })
	 */
	private $user;

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
	 * Image constructor.
	 * @param string  $name
	 * @param Gallery $gallery
	 * @param User    $user
	 */
	public function __construct($name, Gallery $gallery, User $user)
	{
		$this->name = $name;
		$this->active = TRUE;
		$this->dateAdd = new \DateTime();
		$this->gallery = $gallery;
		$this->user = $user;
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
	public function getNameWithPath(): string
	{
		return self::GALLERY_PATH . $this->getGallery()->getId() . "/" . $this->getName();
	}

	/**
	 * @return string
	 */
	public function getMiniNameWithPath(): string
	{
		return self::GALLERY_PATH . $this->getGallery()->getId() . "/" . $this->getMiniName();
	}

	public function getUrl(): string
	{
		return  "https://$_SERVER[HTTP_HOST]/" . $this->getNameWithPath();
	}

	/**
	 * @return string
	 */
	public function getMiniName(): string
	{
		$ext = pathinfo($this->name, PATHINFO_EXTENSION);
		$fileName = pathinfo($this->name, PATHINFO_FILENAME);
		return $fileName . '-mini.' . $ext;
	}

	/**
	 * @return string|null
	 */
	public function getDescription(): ?string
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
	 * @return Gallery
	 */
	public function getGallery(): Gallery
	{
		return $this->gallery;
	}

	/**
	 * @return User
	 */
	public function getUser(): User
	{
		return $this->user;
	}

	/**
	 * @param string|null $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}

	/**
	 * @param bool $active
	 */
	public function setActive(bool $active)
	{
		$this->active = $active;
	}


}
