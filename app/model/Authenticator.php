<?php
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
use Nette\Security\IIdentity;
use Nette\Security\Passwords;

class Authenticator implements IAuthenticator
{

    private $users;

    function __construct($users)
    {
        $this->users = $users;
    }


    function authenticate(array $credentials)
    {
        list($user, $password) = $credentials;

        foreach($this->users as $name => $pass) {
            if (strcasecmp($name, $user) === 0) {
                if (Passwords::verify($password, $pass)) {
                    return new Identity($name, 'admin');
                } else {
                    throw new AuthenticationException('Invalid password.', self::INVALID_CREDENTIAL);
                }
            }
        }
        throw new AuthenticationException("User '$user' not found.", self::IDENTITY_NOT_FOUND);
    }
}