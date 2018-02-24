<?php declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: Jan
 * Date: 17.5.14
 * Time: 18:42
 */

namespace Model;

use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\Passwords;

class Authenticator implements IAuthenticator
{

	/** @var mixed[] */
	private $users;

	/**
	 * @param mixed[] $users
	 */
	public function __construct(array $users)
	{
		$this->users = $users;
	}

	/**
	 * @param mixed[] $credentials
	 * @return \Nette\Security\Identity|\Nette\Security\IIdentity
	 */
	public function authenticate(array $credentials)
	{
		[$user, $password] = $credentials;

		foreach ($this->users as $name => $pass) {
			if (strcasecmp($name, $user) !== 0) {
				continue;
			}

			if (Passwords::verify($password, $pass)) {
				return new Identity($name, 'admin');
			}

			throw new AuthenticationException('Invalid password.', self::INVALID_CREDENTIAL);
		}
		throw new AuthenticationException("User '$user' not found.", self::IDENTITY_NOT_FOUND);
	}

}
