<?php

namespace Incenteev\WebBundle\Avatar;

interface PathResolverInterface
{
    const TYPE_AVATAR = 'avatar';
    const TYPE_BACKGROUND = 'background';

    /**
     * Gets the path of the uploaded asset for this object.
     *
     * @param object $object
     * @param string $type
     *
     * @return string|null
     */
    public function getPath($object, $type);
}
