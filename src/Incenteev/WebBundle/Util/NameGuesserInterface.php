<?php

namespace Incenteev\WebBundle\Util;

use Incenteev\WebBundle\Entity\User;

interface NameGuesserInterface
{
    /**
     * Guesses the names of a user.
     *
     * This should be a no-op if they are already known to
     * avoid overwriting existing values.
     *
     * @param User $user
     */
    public function guessNames(User $user);
}
