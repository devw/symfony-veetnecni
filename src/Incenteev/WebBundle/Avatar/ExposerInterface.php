<?php

namespace Incenteev\WebBundle\Avatar;

interface ExposerInterface
{
    /**
     * Gets the url of the avatar of the user.
     *
     * @param object $object
     * @param string $type
     *
     * @return string|null
     */
    public function getUrl($object, $type);
}
