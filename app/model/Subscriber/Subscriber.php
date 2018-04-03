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
 * Subscriber
 *
 * @ORM\Table(name="subscriber")
 * @ORM\Entity()
 */
class Subscriber
{

	Use Identifier;

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
	 * @var string
	 *
	 * @ORM\Column(name="email", type="string", nullable=true)
	 */
	private $email;

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
	 * Subscriber constructor.
	 * @param string $email
	 * @param Blog   $blog
	 */
	public function __construct(string $email, Blog $blog)
	{
		$this->email = $email;
		$this->active = TRUE;
		$this->dateAdd = new \DateTime();
		$this->blog = $blog;
	}

	/**
	 * @return Blog
	 */
	public function getBlog(): Blog
	{
		return $this->blog;
	}

	/**
	 * @return string
	 */
	public function getEmail(): string
	{
		return $this->email;
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
	 * @param bool $active
	 */
	public function setActive(bool $active)
	{
		$this->active = $active;
	}

	/**
	 * @param Blog $blog
	 */
	public function setBlog(Blog $blog)
	{
		$this->blog = $blog;
	}

	/**
	 * @param string $email
	 */
	public function setEmail(string $email): void
	{
		$this->email = $email;
	}



}
