<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\String\Slugger\AsciiSlugger;

class InternalAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly string $appInfoInternalHeaderKey
    ) {
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning false will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        return $request->headers->has($this->appInfoInternalHeaderKey);
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     *
     * @return array{key: string|null, name: string, email: string, roles: string[]}
     */
    public function getCredentials(Request $request): array
    {
        $headers = $request->headers;
        $name = mb_substr($headers->get($this->appInfoInternalHeaderKey) ?? 'internal', 0, 36);
        $slugger = new AsciiSlugger();

        return [
            'key' => $headers->get($this->appInfoInternalHeaderKey),
            'name' => $name,
            'email' => sprintf('%s@internal', $slugger->slug($name)),
            'roles' => ['ROLE_INTERNAL'],
        ];
    }

    /**
     * @param array{key: string|null, name: string} $credentials
     */
    public function checkCredentials(array $credentials): bool
    {
        // check credentials - e.g. make sure the password is valid
        // no credential check is needed in this case

        // return true to cause authentication success
        return $credentials['key'] === $this->appInfoInternalHeaderKey;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = ['message' => strtr($exception->getMessageKey(), $exception->getMessageData())];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    public function authenticate(Request $request): Passport
    {
        $credentials = $this->getCredentials($request);

        if (!$this->checkCredentials($credentials)) {
            throw new CustomUserMessageAuthenticationException('Authentication Required');
        }

        $user = new InternalUser();
        $user->setEmail($credentials['email']);
        $user->setUsername($credentials['name']);
        $user->setRoles($credentials['roles']);
        $user->setActive(true);

        return new Passport(
            new UserBadge($credentials['name'], fn () => $user),
            new CustomCredentials(fn () => true, $credentials)
        );
    }
}
