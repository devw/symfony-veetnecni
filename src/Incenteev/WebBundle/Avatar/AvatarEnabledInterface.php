<?php

namespace Incenteev\WebBundle\Avatar;

interface AvatarEnabledInterface
{
    /**
     * Gets the path of the avatar of the user.
     *
     * @return string|null
     */
    public function getAvatarPath();

    /**
     * Sets the path of the avatar of the user.
     *
     * @param string $path
     *
     * @return self
     */
    public function setAvatarPath($path);
}
