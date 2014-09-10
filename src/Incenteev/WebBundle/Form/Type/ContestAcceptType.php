<?php

namespace Incenteev\WebBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContestAcceptType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('accepted', 'checkbox', array(
            'label' => $options['rules_label'],
            'attr' => array(
                'class' => 'form-checkbox',
            ),
        ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Incenteev\WebBundle\Entity\Participation',
            'validation_groups' => 'accept',
            'rules_label' => 'confirmation.label.accept_without_rules',
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'incenteev_contest_accept';
    }
}
