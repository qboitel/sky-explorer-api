<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Security\InternalUser;
use App\Security\ServiceUser;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserActiveVoter extends Voter
{
    public function __construct(
        private readonly Security $security,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $this->security->getUser() instanceof UserInterface;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        /** @var User|InternalUser|ServiceUser $user */
        $user = $this->security->getUser();

        return $user->isActive();
    }
}
