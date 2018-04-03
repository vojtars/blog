<?php declare(strict_types=1);
/**
 * Copyright (c) 2018. 
 * Name: Vojtěch Hlaváček
 * E-mail: vojtechlavacek@gmail.com
 * Www: https://vojtars.cz
 */

namespace Vojtars\Model;

use Doctrine\ORM\EntityManager;
use Nette;
use Nette\Security\Passwords;
use Tracy\Debugger;

/**
 * Users management.
 */
class UserManager implements Nette\Security\IAuthenticator
{
	use Nette\SmartObject;

	/**
	 * @var EntityManager
	 */
	private $entityManager;


	/**
	 * UserManager constructor.
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * Performs an authentication.
	 * @param array $credentials
	 * @return Nette\Security\Identity
	 * @throws \Doctrine\ORM\ORMException
	 * @throws \Doctrine\ORM\OptimisticLockException
	 * @throws \Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;

		/** @var User $user */
		$user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $username]);

		if (empty($user)) {
			throw new Nette\Security\AuthenticationException('Uživatel s tímto e-mailem neexistuje', self::IDENTITY_NOT_FOUND);
		} elseif (!Passwords::verify($password, $user->getPassword())) {
			throw new Nette\Security\AuthenticationException('Neplatné heslo.', self::INVALID_CREDENTIAL);
		}

		$user->changeLoginData();
		$this->entityManager->flush($user);
		return new Nette\Security\Identity($user->getId(), $user->getRoles(), $user->toArray());
	}

}


class DuplicateNameException extends \Exception
{
}
