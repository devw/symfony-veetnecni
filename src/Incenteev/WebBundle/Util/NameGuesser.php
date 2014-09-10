<?php

namespace Incenteev\WebBundle\Util;

use Incenteev\WebBundle\Entity\User;

class NameGuesser implements NameGuesserInterface
{
    /**
     * {@inheritDoc}
     */
    public function guessNames(User $user)
    {
        // Don't guess the names if they are already known
        if (null !== $user->getFirstName() || null !== $user->getLastName()) {
            return;
        }

        $email = $user->getEmail();

        if (null === $email || false === $pos = strpos($email, '@')) {
            return;
        }

        $names = explode('.', substr($email, 0, $pos), 2);

        if (2 === count($names)) {
            $user->setFirstName(trim($names[0]))
                ->setLastName(str_replace('.', ' ', trim($names[1])));
        } else {
            $user->setFirstName(trim($names[0]))
                ->setLastName('');
        }
    }
}
