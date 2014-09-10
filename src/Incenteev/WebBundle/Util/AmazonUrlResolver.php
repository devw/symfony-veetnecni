<?php

namespace Incenteev\WebBundle\Util;

class AmazonUrlResolver implements BaseUrlResolverInterface
{
    private $bucket;
    private $region;

    /**
     * @param string $bucket
     * @param string $region
     */
    public function __construct($bucket, $region = \AmazonS3::REGION_EU_W1)
    {
        $this->bucket = $bucket;
        $this->region = $region;
    }

    /**
     * {@inheritDoc}
     */
    public function getBaseAssetUrl()
    {
        return sprintf('https://%s/%s', $this->region, $this->bucket);
    }
}
