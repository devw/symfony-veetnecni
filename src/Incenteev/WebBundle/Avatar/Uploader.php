<?php

namespace Incenteev\WebBundle\Avatar;

use Gaufrette\Filesystem;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Imagine\Image\Box;

class Uploader
{
    private $filesystem;
    private $imagine;
    private $pathResolver;

    /**
     * @param Filesystem            $filesystem
     * @param ImagineInterface      $imagine
     * @param PathResolverInterface $pathResolver
     */
    public function __construct(Filesystem $filesystem, ImagineInterface $imagine, PathResolverInterface $pathResolver)
    {
        $this->filesystem = $filesystem;
        $this->imagine = $imagine;
        $this->pathResolver = $pathResolver;
    }

    /**
     * Gets the url of the avatar of the user.
     *
     * @param object $object
     * @param string $type
     * @param string $content
     *
     * @throws \InvalidArgumentException
     *
     */
    public function upload($object, $type, $content)
    {
        switch ($type) {
            case PathResolverInterface::TYPE_AVATAR:
                $this->uploadAvatar($object, $content);
                break;

            case PathResolverInterface::TYPE_BACKGROUND:
                $this->uploadBackground($object, $content);
                break;

            default:
                throw new \InvalidArgumentException(sprintf('Unsupported type "%s"', $type));
        }
    }

    private function uploadAvatar(AvatarEnabledInterface $object, $content)
    {
        $oldPath = $this->pathResolver->getPath($object, PathResolverInterface::TYPE_AVATAR);

        $object->setAvatarPath(md5(uniqid(mt_rand(), true)).'.png');
        $newPath = $this->pathResolver->getPath($object, PathResolverInterface::TYPE_AVATAR);

        $size = new Box(200, 200);

        try {
            $uploadedImage = $this->imagine->load($content);
            /** @var $resizedImage \Imagine\Image\ImageInterface */
            $resizedImage = $uploadedImage->thumbnail($size, ImageInterface::THUMBNAIL_OUTBOUND);

            $this->filesystem->write($newPath, $resizedImage->get('png'), true, array('contentType' => 'image/png'));
        } catch (\Exception $e) {
            $object->setAvatarPath($oldPath);

            throw $e;
        }

        try {
            $this->filesystem->delete($oldPath);
        } catch (\Exception $e) {
            // ignore the error here. It will simply let a useless file.
        }
    }

    private function uploadBackground(BackgroundEnabledInterface $object, $content)
    {
        $rawOldPath = $object->getBackgroundPath();
        $oldPath = $this->pathResolver->getPath($object, PathResolverInterface::TYPE_BACKGROUND);

        $object->setBackgroundPath(md5(uniqid()).'.png');
        $newPath = $this->pathResolver->getPath($object, PathResolverInterface::TYPE_BACKGROUND);

        try {
            $uploadedImage = $this->imagine->load($content);

            $imageSize = $uploadedImage->getSize();

            $ratios = array(
                1920 / $imageSize->getWidth(),
                1080 / $imageSize->getHeight(),
                1, // Don't scale up
            );

            $ratio = min($ratios); // Scale according to the bigger dimension of the image
            $size = $imageSize->scale($ratio);

            $uploadedImage->resize($size);

            $this->filesystem->write($newPath, $uploadedImage->get('png'), true, array('contentType' => 'image/png'));
        } catch (\Exception $e) {
            $object->setBackgroundPath($oldPath);

            throw $e;
        }

        // Remove the old background from the S3 if it is not a built-in one.
        if (!empty($rawOldPath) && 0 !== strpos($rawOldPath, 'background/')) {
            try {
                $this->filesystem->delete($oldPath);
            } catch (\Exception $e) {
                // ignore the error here. It will simply let a useless file.
            }
        }
    }
}
