<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AuthEventsSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly NormalizerInterface $normalizer,
        private readonly JWTEncoderInterface $jwtEncoder,
        private readonly JWTTokenManagerInterface $jwtManager,
        private readonly UserRepository $userRepository
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::AUTHENTICATION_SUCCESS => 'onAuthenticationSuccess',
            Events::JWT_CREATED => 'onJWTCreatedEvent',
            LogoutEvent::class => 'onLogout',
        ];
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $data = [];
        /** @var User $user */
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        $payload = $this->jwtEncoder->decode((string) $event->getData()['token']);
        $data['user'] = $this->normalizer->normalize($user);
        $data['token'] = $event->getData()['token'];
        $data['tokenExpiresAt'] = $payload['exp'];
        $data['refreshTokenExpiresAt'] = $event->getData()['refresh_token_expiration'];

        $this->userRepository->add($user->setLastConnectedAt(new \DateTimeImmutable()), true);
        $event->setData($data);
    }

    public function onJWTCreatedEvent(JWTCreatedEvent $event): void
    {
        $event->setData([
            $this->jwtManager->getUserIdClaim() => $event->getUser()->getUserIdentifier(),
        ]);
    }

    public function onLogout(LogoutEvent $event): void
    {
        $response = $event->getResponse();
        $response->headers->clearCookie('BEARER');
    }
}
