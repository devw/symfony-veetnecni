<?php

namespace Incenteev\WebBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrganizationType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'organization.label.name',
            ))
            ->add('language', 'choice', array(
                'label' => 'organization.label.language',
                'choices' => array(
                    'en' => \Locale::getDisplayLanguage('en'),
                    'fr' => \Locale::getDisplayLanguage('fr'),
                ),
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Incenteev\WebBundle\Entity\Organization',
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'incenteev_organization';
    }
}
