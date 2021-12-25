<?php

declare(strict_types=1);

namespace TechnoBureau\User\Repository;

use TechnoBureau\User\Entity\AuthUser as User;
use TechnoBureau\User\Entity\OAuthAccessToken;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use League\OAuth2\Server as OAuth;

/**
 * OauthAccessTokenRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class OauthAccessTokenRepository extends \Doctrine\ORM\EntityRepository  implements OAuth\Repositories\AccessTokenRepositoryInterface
{
  /**
     * @throws ORMException
     */
    public function getNewToken(
      OAuth\Entities\ClientEntityInterface $clientEntity,
      array $scopes,
      $userIdentifier = null
  ): OAuthAccessToken {
      $accessToken = new OAuthAccessToken();
      $accessToken->setClient($clientEntity);
      foreach ($scopes as $scope) {
          $accessToken->addScope($scope);
      }
      if ($userIdentifier !== null) {
          $accessToken->setUser($this->getEntityManager()->getReference(User::class, $userIdentifier));
      }
      return $accessToken;
  }

  /**
   * @return void
   * @throws ORMException
   *
   * @throws OptimisticLockException
   */
  public function persistNewAccessToken(OAuth\Entities\AccessTokenEntityInterface $accessTokenEntity)
  {
      $this->getEntityManager()->persist($accessTokenEntity);
      $this->getEntityManager()->flush();
  }

  /**
   * @param string $tokenId
   *
   * @return void
   * @throws ORMException
   *
   */
  public function revokeAccessToken($tokenId)
  {
      /** @var ?OAuthAccessToken $accessTokenEntity */
      $accessTokenEntity = $this->findOneBy(['token' => $tokenId]);
      if (null === $accessTokenEntity) {
          return;
      }
      $accessTokenEntity->setIsRevoked(true);
      $this->getEntityManager()->persist($accessTokenEntity);
      $this->getEntityManager()->flush();
  }

  public function isAccessTokenRevoked($tokenId): bool
  {
      /** @var ?OAuthAccessToken $accessTokenEntity */
      $accessTokenEntity = $this->findOneBy(['token' => $tokenId]);

      if (null === $accessTokenEntity) {
          return true;
      }

      return $accessTokenEntity->getIsRevoked();
  }
}
