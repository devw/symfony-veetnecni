<?php

namespace Incenteev\WebBundle\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType;
use Incenteev\WebBundle\Form\EventListener\NameGuesserListener;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends RegistrationFormType
{
    private $nameGuesserListener;

    public function __construct($class, NameGuesserListener $nameGuesserListener)
    {
        parent::__construct($class);

        $this->nameGuesserListener = $nameGuesserListener;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', array(
                'label' => 'form.email',
                'translation_domain' => 'FOSUserBundle',
                'attr' => array('placeholder' => 'form.email'),
            ))
            ->add('organization_name', 'text', array(
                'label' => 'registration.label.organization',
                'property_path' => 'organization.name',
                'attr' => array('placeholder' => 'registration.label.organization'),
            ))
            ->add('plainPassword', 'password', array(
                'translation_domain' => 'FOSUserBundle',
                'label' => 'form.password',
                'attr' => array('placeholder' => 'form.password'),
            ))
            ->addEventSubscriber($this->nameGuesserListener)
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'incenteev_registration';
    }
}
