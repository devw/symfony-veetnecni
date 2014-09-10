<?php

namespace Incenteev\WebBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\File;

class AvatarType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $constraints = function (Options $options) {
            $groups = $options['validation_groups'];

            $group = is_array($groups) ? array(current($groups)) : $groups;

            return array(new File(array(
                'maxSize' => '2M',
                'mimeTypes' => array('image/png', 'image/x-png', 'image/jpeg', 'image/pjpeg'),
                'groups' => $group,
            )));
        };

        $resolver->setDefaults(array(
            'mapped' => false,
            'required' => false,
            'constraints' => $constraints,
        ));
    }

    public function getParent()
    {
        return 'file';
    }

    public function getName()
    {
        return 'incenteev_avatar';
    }
}
