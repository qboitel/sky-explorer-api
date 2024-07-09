<?php

namespace App\Security;

use App\Entity\User;

class InternalUser extends User
{
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->getEmail();
    }
}
