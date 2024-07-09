<?php

namespace App\Entity;

use App\Traits\BlameableTrait;
use App\Traits\TimestampableTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Entity\RefreshTokenRepository;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: RefreshTokenRepository::class)]
#[ORM\Table('refresh_tokens')]
class RefreshToken implements RefreshTokenInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'SEQUENCE')]
    #[ORM\Column(type: Types::INTEGER)]
    protected int $id;

    #[ORM\Column(type: Types::STRING, length: 128, unique: true)]
    protected string $refreshToken;

    #[ORM\Column(type: Types::STRING, length: 255)]
    protected string $username;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    protected \DateTimeInterface $valid;

    /**
     * Creates a new model instance based on the provided details.
     */
    public static function createForUserWithTtl(string $refreshToken, UserInterface $user, int $ttl): RefreshTokenInterface
    {
        $valid = new \DateTime();

        // Explicitly check for a negative number based on a behavior change in PHP 8.2, see https://github.com/php/php-src/issues/9950
        if ($ttl > 0) {
            $valid->modify('+'.$ttl.' seconds');
        } elseif ($ttl < 0) {
            $valid->modify($ttl.' seconds');
        }

        $model = new self();
        $model->setRefreshToken($refreshToken);
        $model->setUsername($user->getUserIdentifier());
        $model->setValid($valid);

        return $model;
    }

    /**
     * @return string Refresh Token
     */
    public function __toString(): string
    {
        return $this->getRefreshToken() ?: '';
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function setRefreshToken($refreshToken = null)
    {
        if (null === $refreshToken || '' === $refreshToken) {
            trigger_deprecation('gesdinet/jwt-refresh-token-bundle', '1.0', 'Passing an empty token to %s() to automatically generate a token is deprecated.', __METHOD__);

            $refreshToken = bin2hex(random_bytes(64));
        }

        $this->refreshToken = $refreshToken;

        return $this;
    }

    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    public function setValid($valid)
    {
        $this->valid = $valid;

        return $this;
    }

    public function getValid()
    {
        return $this->valid;
    }

    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function isValid()
    {
        return $this->valid instanceof \DateTimeInterface && $this->valid >= new \DateTime();
    }
}
