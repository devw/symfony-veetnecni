<?php

namespace Incenteev\WebBundle\Avatar;

use Gaufrette\Filesystem;
use Incenteev\WebBundle\Entity\User;
use Incenteev\WebBundle\Util\BaseUrlResolverInterface;

class Exposer implements ExposerInterface
{
    private $assetsUrlResolver;
    private $baseUrlResolver;
    private $defaultGravatar;
    private $filesystem;
    private $pathResolver;

    /**
     * @param PathResolverInterface    $pathResolver
     * @param BaseUrlResolverInterface $assetsUrlResolver
     * @param BaseUrlResolverInterface $baseUrlResolver
     * @param Filesystem               $filesystem
     * @param string                   $defaultGravatar   The default gravatar image
     */
    public function __construct(PathResolverInterface $pathResolver, BaseUrlResolverInterface $assetsUrlResolver, BaseUrlResolverInterface $baseUrlResolver, Filesystem $filesystem, $defaultGravatar)
    {
        $this->pathResolver = $pathResolver;
        $this->assetsUrlResolver = $assetsUrlResolver;
        $this->baseUrlResolver = $baseUrlResolver;
        $this->filesystem = $filesystem;
        $this->defaultGravatar = $defaultGravatar;
    }

    /**
     * Gets the url of the avatar of the user.
     *
     * @param object $object
     * @param string $type
     *
     * @return string|null
     *
     * @throws \InvalidArgumentException
     */
    public function getUrl($object, $type)
    {
        switch ($type) {
            case PathResolverInterface::TYPE_AVATAR:
                return $this->getAvatar($object);

            case PathResolverInterface::TYPE_BACKGROUND:
                return $this->getBackground($object);

            default:
                return $this->getUploadedImageUrl($type, $object);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getGravatarUrl(User $user)
    {
        $hash = md5(strtolower(trim($user->getEmail())));
        $url = 'https://secure.gravatar.com/avatar/' . $hash . '?' . http_build_query(array_filter(array(
            'd' => $this->defaultGravatar,
            's' => 200,
        )), '', '&');

        return $url;
    }

    private function getAvatar(AvatarEnabledInterface $object)
    {
        $url = $this->getUploadedImageUrl(PathResolverInterface::TYPE_AVATAR, $object);

        if (null !== $url) {
            return $url;
        }

        if ($object instanceof User) {
            return $this->getGravatarUrl($object);
        }

        return null;
    }

    private function getBackground(BackgroundEnabledInterface $object)
    {
        $rawPath = $object->getBackgroundPath();

        if (!empty($rawPath) && 0 === strpos($rawPath, 'background/')) {
            return sprintf('%s/%s', $this->assetsUrlResolver->getBaseAssetUrl(), $rawPath);
        }

        return $this->getUploadedImageUrl(PathResolverInterface::TYPE_BACKGROUND, $object);
    }

    private function getUploadedImageUrl($type, $object)
    {
        $path = $this->pathResolver->getPath($object, $type);

        if (null === $path) {
            return null;
        }

        if ($this->filesystem->has($path)) {
            return sprintf('%s/%s', $this->baseUrlResolver->getBaseAssetUrl(), $path);
        }

        return null;
    }
}
