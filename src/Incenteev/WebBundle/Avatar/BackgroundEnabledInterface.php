<?php

namespace Incenteev\WebBundle\Avatar;

interface BackgroundEnabledInterface
{
    /**
     * Gets the path of the background of the object.
     *
     * @return string|null
     */
    public function getBackgroundPath();

    /**
     * Sets the path of the background of the object.
     *
     * @param string $path
     *
     * @return self
     */
    public function setBackgroundPath($path);
}
