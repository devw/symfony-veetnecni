<?php

namespace Incenteev\WebBundle\Avatar;

use Incenteev\WebBundle\Entity\Contest;
use Incenteev\WebBundle\Entity\OrganizationTeam;
use Incenteev\WebBundle\Entity\ContestTeam;
use Incenteev\WebBundle\Entity\Prize;
use Incenteev\WebBundle\Entity\User;

class PathResolver implements PathResolverInterface
{
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
    public function getPath($object, $type)
    {
        switch ($type) {
            case self::TYPE_AVATAR:
                return $this->getAvatarPath($object);

            case self::TYPE_BACKGROUND:
                return $this->getBackgroundPath($object);

            default:
                throw new \InvalidArgumentException(sprintf('Unsupported type "%s"', $type));
        }
    }

    private function getAvatarPath(AvatarEnabledInterface $object)
    {
        return $this->getUploadedImagePath(self::TYPE_AVATAR, $object, $object->getAvatarPath());
    }

    private function getBackgroundPath(BackgroundEnabledInterface $object)
    {
        return $this->getUploadedImagePath(self::TYPE_BACKGROUND, $object, $object->getBackgroundPath());
    }

    private function getUploadedImagePath($type, $object, $path)
    {
        if (null === $path) {
            return null;
        }

        return sprintf('%s/%s/%s', $type, $this->getObjectType($object), $path);
    }

    private function getObjectType($object)
    {
        switch (true) {
            case ($object instanceof User):
                return 'user';

            case ($object instanceof Contest):
                return 'contest';

            case ($object instanceof Prize):
                return 'prize';

            case ($object instanceof OrganizationTeam):
                return 'organization-team';

            case ($object instanceof ContestTeam):
                return 'contest-team';

            default:
                throw new \InvalidArgumentException(sprintf('Unsupported object "%s"', get_class($object)));
        }
    }
}
