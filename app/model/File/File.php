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

/**
 * File
 *
 * @ORM\Table(name="file")
 * @ORM\Entity()
 *
 */
class File
{

	Use Identifier;


	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", nullable=true)
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="file_name", type="string", nullable=true)
	 */
	private $fileName;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="size", type="integer", nullable=true)
	 */
	private $size;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="date_add", type="datetime", nullable=true)
	 */
	private $dateAdd;

	/**
	 * @var \Vojtars\Model\PostHasFile[]|\Doctrine\Common\Collections\ArrayCollection
	 *
	 * @ORM\OneToMany(targetEntity="\Vojtars\Model\PostHasFile", mappedBy="file")
	 */
	private $postHasFiles;

	/**
	 * File constructor.
	 * @param string $fileName
	 * @param int    $size
	 */
	public function __construct(string $fileName, int $size)
	{
		$this->fileName = $fileName;
		$this->dateAdd = new \DateTime();
		$this->size = $size;
	}

	/**
	 * @return string|NULL
	 */
	public function getName(): ?string
	{
		return $this->name;
	}

	/**
	 * @return int
	 */
	public function getSize(): int
	{
		return $this->size;
	}

	/**
	 * @return string
	 */
	public function getFileName(): string
	{
		return $this->fileName;
	}

	/**
	 * @return \DateTime
	 */
	public function getDateAdd(): \DateTime
	{
		return $this->dateAdd;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name): void
	{
		$this->name = $name;
	}

	/**
	 * @param string $fileName
	 */
	public function setFileName(string $fileName): void
	{
		$this->fileName = $fileName;
	}

	/**
	 * @param \DateTime $dateAdd
	 */
	public function setDateAdd(\DateTime $dateAdd): void
	{
		$this->dateAdd = $dateAdd;
	}

	public function getUrl(): string
	{
		return "https://$_SERVER[HTTP_HOST]/files/" . $this->getFileName();
	}

	/**
	 * @return \Doctrine\Common\Collections\ArrayCollection|PostHasFile[]
	 */
	public function getPostHasFiles()
	{
		return $this->postHasFiles;
	}


}
