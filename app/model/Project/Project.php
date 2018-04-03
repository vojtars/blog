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
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity()
 */
class Project
{

	Use Identifier;
	Use EntityValidator;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", nullable=true)
	 */
	private $name;

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
	 * @ORM\Column(name="description", type="string", nullable=true)
	 */
	private $description;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="name_tab1", type="string", nullable=true)
	 */
	private $nameTab1;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="name_tab2", type="string", nullable=true)
	 */
	private $nameTab2;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="name_tab3", type="string", nullable=true)
	 */
	private $nameTab3;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="tab1", type="string", nullable=true)
	 */
	private $tab1;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="tab2", type="string", nullable=true)
	 */
	private $tab2;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="tab3", type="string", nullable=true)
	 */
	private $tab3;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="show_tab1", type="boolean", nullable=false)
	 */
	private $showTab1 = TRUE;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="show_tab2", type="boolean", nullable=false)
	 */
	private $showTab2 = TRUE;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="show_tab3", type="boolean", nullable=false)
	 */
	private $showTab3 = TRUE;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="position", type="integer", nullable=true)
	 */
	private $position;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="date_add", type="datetime", nullable=false)
	 */
	private $dateAdd;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="active", type="boolean", nullable=false)
	 */
	private $active = FALSE;

	/**
	 * Project constructor.
	 * @param string $name
	 */
	public function __construct(string $name)
	{
		$this->name = $name;
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
	 * @return Image|null
	 */
	public function getImage(): ?Image
	{
		return $this->checkImage($this->image);
	}

	/**
	 * @return string|null
	 */
	public function getDescription(): ?string
	{
		return $this->description;
	}

	/**
	 * @return string|null
	 */
	public function getNameTab1(): ?string
	{
		return $this->nameTab1;
	}

	/**
	 * @return string|null
	 */
	public function getNameTab2(): ?string
	{
		return $this->nameTab2;
	}

	/**
	 * @return string|null
	 */
	public function getNameTab3(): ?string
	{
		return $this->nameTab3;
	}

	/**
	 * @return string|null
	 */
	public function getTab1(): ?string
	{
		return $this->tab1;
	}

	/**
	 * @return string|null
	 */
	public function getTab2(): ?string
	{
		return $this->tab2;
	}

	/**
	 * @return string|null
	 */
	public function getTab3(): ?string
	{
		return $this->tab3;
	}

	/**
	 * @return bool
	 */
	public function isShowTab1(): bool
	{
		return $this->showTab1;
	}

	/**
	 * @return bool
	 */
	public function isShowTab2(): bool
	{
		return $this->showTab2;
	}

	/**
	 * @return bool
	 */
	public function isShowTab3(): bool
	{
		return $this->showTab3;
	}

	/**
	 * @return int|null
	 */
	public function getPosition(): ?int
	{
		return $this->position;
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
	 * @param string $name
	 */
	public function setName(string $name): void
	{
		$this->name = $name;
	}

	/**
	 * @param string $description
	 */
	public function setDescription(string $description): void
	{
		$this->description = $description;
	}

	/**
	 * @param string $nameTab1
	 */
	public function setNameTab1(string $nameTab1): void
	{
		$this->nameTab1 = $nameTab1;
	}

	/**
	 * @param string $nameTab2
	 */
	public function setNameTab2(string $nameTab2): void
	{
		$this->nameTab2 = $nameTab2;
	}

	/**
	 * @param string $nameTab3
	 */
	public function setNameTab3(string $nameTab3): void
	{
		$this->nameTab3 = $nameTab3;
	}

	/**
	 * @param string $tab1
	 */
	public function setTab1(string $tab1): void
	{
		$this->tab1 = $tab1;
	}

	/**
	 * @param string $tab2
	 */
	public function setTab2(string $tab2): void
	{
		$this->tab2 = $tab2;
	}

	/**
	 * @param string $tab3
	 */
	public function setTab3(string $tab3): void
	{
		$this->tab3 = $tab3;
	}

	/**
	 * @param bool $showTab1
	 */
	public function setShowTab1(bool $showTab1): void
	{
		$this->showTab1 = $showTab1;
	}

	/**
	 * @param bool $showTab2
	 */
	public function setShowTab2(bool $showTab2): void
	{
		$this->showTab2 = $showTab2;
	}

	/**
	 * @param bool $showTab3
	 */
	public function setShowTab3(bool $showTab3): void
	{
		$this->showTab3 = $showTab3;
	}

	/**
	 * @param int $position
	 */
	public function setPosition(int $position): void
	{
		$this->position = $position;
	}

	/**
	 * @param bool $active
	 */
	public function setActive(bool $active): void
	{
		$this->active = $active;
	}

	public function setTabs($tab1, $tab2, $tab3)
	{
		$this->tab1 = $tab1;
		$this->tab2 = $tab2;
		$this->tab3 = $tab3;
	}

	public function setTabNames($nameTab1, $nameTab2, $nameTab3)
	{
		$this->nameTab1 = $nameTab1;
		$this->nameTab2 = $nameTab2;
		$this->nameTab3 = $nameTab3;
	}

	/**
	 * @param Image $image
	 */
	public function setImage(Image $image)
	{
		$this->image = $image;
	}


}
