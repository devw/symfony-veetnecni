<?php

namespace Incenteev\WebBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class InvitedRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array(
                'label' => 'user.label.email',
                'disabled' => true,
            ))
            ->add('plainPassword', 'password', array(
                'label' => 'user.label.password',
            ))
            ->add('avatar', 'incenteev_avatar', array(
                'label' => 'user.label.avatar',
                'validation_groups' => $options['validation_groups'],
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Incenteev\WebBundle\Entity\User',
            'validation_groups' => array('FullRegistration'),
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'incenteev_invited_registration';
    }
}
