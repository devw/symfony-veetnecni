<?php

namespace Incenteev\WebBundle\Util;

interface BaseUrlResolverInterface
{
    /**
     * Gets the base url for assets
     *
     * @return string
     */
    public function getBaseAssetUrl();
}
