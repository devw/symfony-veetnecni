<?php

namespace Incenteev\WebBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfileType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text', array(
                'label' => 'user.label.first_name',
            ))
            ->add('lastName', 'text', array(
                'label' => 'user.label.last_name',
            ))
            ->add('avatar', 'incenteev_avatar', array(
                'label' => 'user.label.avatar',
                'validation_groups' => $options['validation_groups'],
            ))
            // TODO check if the avatar is defined
            ->add('remove_avatar', 'checkbox', array(
                'required' => false,
                'mapped' => false,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Incenteev\WebBundle\Entity\User',
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'incenteev_profile';
    }
}
