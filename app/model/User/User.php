<?php declare(strict_types=1);
/**
 * Copyright (c) 2018.
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Model;

use Kdyby\Doctrine\Entities\Attributes\Identifier;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity()
 */
class User implements IIdentity
{

	use Identifier;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", length=255, nullable=false)
	 */
	private $name;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="surname", type="string", length=255, nullable=false)
	 */
	private $surname;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="email", type="string", length=100, nullable=false, unique=true)
	 */
	private $email;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="phone", type="string", length=50, nullable=true)
	 */
	private $phone;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="password", type="string", length=255, nullable=false)
	 */
	private $password;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="date_add", type="datetime", nullable=true)
	 */
	private $dateAdd;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="enabled", type="boolean", options={"default"=1})
	 */
	private $enabled;

	/**
	 * @var \DateTime
	 *
	 * @ORM\Column(name="date_last_login", type="datetime", nullable=true)
	 */
	private $dateLastLogin;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="ip_last_login", type="string", length=45, nullable=true)
	 */
	private $ipLastLogin;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="street", type="string", length=255, nullable=false)
	 */
	private $street;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="city", type="string", length=255, nullable=false)
	 */
	private $city;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="avatar", type="string", length=255, nullable=false)
	 */
	private $avatar;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="zip", type="string", length=20, nullable=false)
	 */
	private $zip;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="state", type="string", length=255, nullable=false)
	 */
	private $state;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="company", type="string", length=255, nullable=true)
	 */
	private $company;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="ic", type="string", length=50, nullable=true)
	 */
	private $ic;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="dic", type="string", length=50, nullable=true)
	 */
	private $dic;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="total_sign_in", type="integer", nullable=false)
	 */
	private $totalSignIn = '0';

	/**
	 * @var string
	 *
	 * @ORM\Column(name="ip_register", type="string", length=45, nullable=true)
	 */
	private $ipRegister;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="roles", type="string", length=45, nullable=false)
	 */
	private $roles;


	/**
	 * User constructor.
	 * @param string $email
	 */
	public function __construct(string $email)
	{
		$this->email = $email;
		$this->dateAdd = new \DateTime();
		$this->roles = 'client';

		if (!empty($_SERVER['REMOTE_ADDR'])) {
			$this->ipRegister = $_SERVER['REMOTE_ADDR'];
		}
		$this->enabled = TRUE;
	}

	/**
	 * @param string $newPassword
	 */
	public function changePassword(string $newPassword)
	{
		$this->password = Passwords::hash($newPassword);
	}

	public function changeLoginData()
	{
		if (!empty($_SERVER['REMOTE_ADDR'])) {
			$this->ipLastLogin = $_SERVER['REMOTE_ADDR'];
		}
		$this->dateLastLogin = new \DateTime();
	}

	/**
	 * @return null|string
	 */
	public function getFullName(): ?string
	{
		if (!empty($this->name) && !empty($this->surname)) {
			return $this->name . ' ' . $this->surname;
		} elseif (empty($this->name) && !empty($this->surname)) {
			return $this->surname;
		} elseif (!empty($this->name) && empty($this->surname)) {
			return $this->name;
		} else {
			return NULL;
		}
	}

	/**
	 * Returns a list of roles that the user is a member of.
	 * @return array
	 */
	function getRoles()
	{
		return explode(',', $this->roles);
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
	public function getSurname(): string
	{
		return $this->surname;
	}

	/**
	 * @return string
	 */
	public function getPassword(): string
	{
		return $this->password;
	}

	/**
	 * @return string
	 */
	public function getEmail(): string
	{
		return $this->email;
	}

	/**
	 * @return null|string
	 */
	public function getPhone(): ?string
	{
		return $this->phone;
	}

	/**
	 * @return \DateTime
	 */
	public function getDateAdd(): ?\DateTime
	{
		return $this->dateAdd;
	}

	/**
	 * @return bool
	 */
	public function isEnabled(): bool
	{
		return $this->enabled;
	}

	/**
	 * @return \DateTime
	 */
	public function getDateLastLogin(): ?\DateTime
	{
		return $this->dateLastLogin;
	}

	/**
	 * @return string
	 */
	public function getIpLastLogin(): string
	{
		return $this->ipLastLogin;
	}

	/**
	 * @return null|string
	 */
	public function getStreet(): ?string
	{
		return $this->street;
	}

	/**
	 * @return null|string
	 */
	public function getCity(): ?string
	{
		return $this->city;
	}

	/**
	 * @return null|string
	 */
	public function getAvatar(): ?string
	{
		return $this->avatar;
	}

	/**
	 * @return null|string
	 */
	public function getZip(): ?string
	{
		return $this->zip;
	}

	/**
	 * @return null|string
	 */
	public function getState(): ?string
	{
		return $this->state;
	}

	/**
	 * @return null|string
	 */
	public function getCompany(): ?string
	{
		return $this->company;
	}

	/**
	 * @return null|string
	 */
	public function getIc(): ?string
	{
		return $this->ic;
	}

	/**
	 * @return null|string
	 */
	public function getDic(): ?string
	{
		return $this->dic;
	}

	/**
	 * @return int
	 */
	public function getTotalSignIn(): int
	{
		return $this->totalSignIn;
	}


	/**
	 * @return null|string
	 */
	public function getIpRegister(): ?string
	{
		return $this->ipRegister;
	}

	public function toArray()
	{
		return [
			'id' => $this->getId(),
			'name' => $this->getName(),
			'surname' => $this->getSurname(),
			'fullName' => $this->getFullName(),
			'email' => $this->getEmail(),
		];
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name)
	{
		$this->name = $name;
	}

	/**
	 * @param string $surname
	 */
	public function setSurname(string $surname)
	{
		$this->surname = $surname;
	}

	/**
	 * @param string $email
	 */
	public function setEmail(string $email)
	{
		$this->email = $email;
	}

	/**
	 * @param string $phone
	 */
	public function setPhone(string $phone)
	{
		$this->phone = $phone;
	}

	/**
	 * @param string $password
	 */
	public function setPassword(string $password)
	{
		$this->password = $password;
	}

	/**
	 * @param bool $enabled
	 */
	public function setEnabled(bool $enabled)
	{
		$this->enabled = $enabled;
	}

	/**
	 * @param string $street
	 */
	public function setStreet(string $street)
	{
		$this->street = $street;
	}

	/**
	 * @param string $city
	 */
	public function setCity(string $city)
	{
		$this->city = $city;
	}

	/**
	 * @param string $avatar
	 */
	public function setAvatar(string $avatar)
	{
		$this->avatar = $avatar;
	}

	/**
	 * @param string $zip
	 */
	public function setZip(string $zip)
	{
		$this->zip = $zip;
	}

	/**
	 * @param string $state
	 */
	public function setState(string $state)
	{
		$this->state = $state;
	}

	/**
	 * @param string $company
	 */
	public function setCompany(string $company)
	{
		$this->company = $company;
	}

	/**
	 * @param string $ic
	 */
	public function setIc(string $ic)
	{
		$this->ic = $ic;
	}

	/**
	 * @param string $dic
	 */
	public function setDic(string $dic)
	{
		$this->dic = $dic;
	}

	/**
	 * @param string $roles
	 */
	public function setRoles(string $roles)
	{
		$this->roles = $roles;
	}


}
