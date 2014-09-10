<?php

namespace Incenteev\WebBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProfileRegistrationType extends AbstractType
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
            ->add('email', 'email', array(
                'label' => 'user.label.email',
                'disabled' => true,
            ))
            ->add('plainPassword', 'password', array(
                'label' => 'user.label.password',
                'help_text' => 'user.help.password',
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Incenteev\WebBundle\Entity\User',
            'validation_groups' => array('ProfileRegistration'),
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'incenteev_profile_registration';
    }
}
