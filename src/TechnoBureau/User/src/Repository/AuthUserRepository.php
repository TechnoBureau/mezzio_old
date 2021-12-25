<?php

declare(strict_types=1);

namespace TechnoBureau\User\Repository;

use TechnoBureau\Entity\AuthUser as User;
use Doctrine\ORM\EntityRepository;
use League\OAuth2\Server\Entities\UserEntityInterface;
use Mezzio\Authentication\UserInterface;
use Mezzio\Authentication\UserRepositoryInterface as MezzioAuthInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface as OAuthUserRepositoryInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;

/**
 * AuthUserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AuthUserRepository extends EntityRepository implements MezzioAuthInterface, OAuthUserRepositoryInterface
{

  public function getUserEntityByUserCredentials(
    $username,
    $password,
    $grantType,
    ClientEntityInterface $clientEntity
): ?UserEntityInterface {

    /** @var ?User $user */
    $user = $this->findOneBy(['email' => $username]);

    if ($user === null) {
        return null;
    }

    if (password_verify($password, $user->getPassword())) {
        return $user;
    }

    return null;
}

public function authenticate(string $credential, string $password = null): ?UserInterface
{
    /** @var ?User $user */
    $user = $this->findOneBy(['email' => $credential]);

    if ($user === null) {
        return null;
    }

    // If password is null, we're authenticating without password.
    // For example, when using Google or GitHub or Facebook as OAuth2 identity provider
    if ($password === null) {
        return $user;
    }

    if (password_verify($password, $user->getPassword())) {
        return $user;
    }

    return null;
}

}
