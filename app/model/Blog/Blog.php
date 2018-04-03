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
 * Blog
 *
 * @ORM\Table(name="blog")
 * @ORM\Entity()
 */
class Blog
{

	Use Identifier;
	Use EntityValidator;

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
	 * Blog constructor.
	 * @param string $name
	 * @param string $url
	 * @param User   $user
	 */
	public function __construct($name, $url, User $user)
	{
		$this->name = $name;
		$this->url = $url;
		$this->active = TRUE;
		$this->dateAdd = new \DateTime();
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
	 * @return string|null
	 */
	public function getDescription(): ?string
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
	 * @return Image|null
	 */
	public function getImage(): ?Image
	{
		return $this->checkImage($this->image);
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
	 * @return User
	 */
	public function getUser(): User
	{
		return $this->user;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name)
	{
		$this->name = $name;
	}

	/**
	 * @param Image $image
	 */
	public function setImage(Image $image)
	{
		$this->image = $image;
	}

	/**
	 * @param string|null $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}

	/**
	 * @param string $url
	 */
	public function setUrl(string $url): void
	{
		$this->url = $url;
	}

	/**
	 * @param bool $active
	 */
	public function setActive(bool $active): void
	{
		$this->active = $active;
	}


}
