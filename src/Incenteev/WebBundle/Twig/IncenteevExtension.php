<?php

namespace Incenteev\WebBundle\Twig;

use Incenteev\WebBundle\Avatar\PathResolverInterface;
use Incenteev\WebBundle\Avatar\ExposerInterface;
use Incenteev\WebBundle\Util\BaseUrlResolverInterface;

class IncenteevExtension extends \Twig_Extension
{
    private $avatarResolver;
    private $assetsUrlResolver;
    private $baseUrlResolver;
    private $supportAddress;

    public function __construct(ExposerInterface $avatarResolver, BaseUrlResolverInterface $assetsUrlResolver, BaseUrlResolverInterface $baseUrlResolver, $supportAddress)
    {
        $this->avatarResolver = $avatarResolver;
        $this->assetsUrlResolver = $assetsUrlResolver;
        $this->baseUrlResolver = $baseUrlResolver;
        $this->supportAddress = $supportAddress;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'incenteev';
    }

    public function getFunctions()
    {
        return array(
            'get_avatar' => new \Twig_Function_Method($this, 'getAvatar'),
            'get_background' => new \Twig_Function_Method($this, 'getBackground'),
            'get_base_asset_url' => new \Twig_Function_Method($this, 'getBaseAssetUrl'),
            'get_static_asset_url' => new \Twig_Function_Method($this, 'getStaticAssetBaseUrl'),
        );
    }

    public function getFilters()
    {
        return array(
            'float_val' => new \Twig_Filter_Function('floatval'),
        );
    }

    public function getGlobals()
    {
        return array(
            'incenteev_support_mail' => $this->supportAddress,
        );
    }

    public function getAvatar($object)
    {
        return $this->avatarResolver->getUrl($object, PathResolverInterface::TYPE_AVATAR);
    }

    public function getBackground($object)
    {
        return $this->avatarResolver->getUrl($object, PathResolverInterface::TYPE_BACKGROUND);
    }

    public function getBaseAssetUrl()
    {
        return $this->baseUrlResolver->getBaseAssetUrl();
    }

    public function getStaticAssetBaseUrl()
    {
        return $this->assetsUrlResolver->getBaseAssetUrl();
    }
}
