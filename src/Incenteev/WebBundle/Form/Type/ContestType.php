<?php

namespace Incenteev\WebBundle\Form\Type;

use Incenteev\WebBundle\Entity\Contest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'contest.label.name',
            ))
            ->add('avatar', 'incenteev_avatar', array(
                'label' => 'contest.label.avatar',
                'validation_groups' => $options['validation_groups'],
                'help_text' => 'contest.help.avatar',
            ))
            // TODO check if the avatar is defined
            ->add('remove_avatar', 'checkbox', array(
                'required' => false,
                'mapped' => false,
            ))
            ->add('description', 'textarea', array(
                'label' => 'contest.label.description',
                'required' => false,
                'attr' => array(
                    'class' => 'markdown-input',
                ),
            ))
            ->add('rules', 'textarea', array(
                'label' => 'contest.label.rules',
                'required' => false,
                'attr' => array(
                    'class' => 'markdown-input',
                ),
            ))
            ->add('startDate', 'date', array(
                'required' => false,
                'label' => 'contest.label.start_date',
                'widget' => 'single_text',
                'help_text' => $options['strict_edit'] ? 'contest.help.disabled_after_launch' : null,
                'attr' => array(
                    'class' => 'date-picker',
                    'autocomplete' => 'off',
                ),
                'disabled' => $options['strict_edit'],
            ))
            ->add('endDate', 'date', array(
                'required' => false,
                'label' => 'contest.label.end_date',
                'widget' => 'single_text',
                'help_text' => $options['strict_edit'] ? 'contest.help.disabled_after_launch' : null,
                'attr' => array(
                    'class' => 'date-picker',
                    'autocomplete' => 'off',
                ),
                'disabled' => $options['strict_edit'],
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Incenteev\WebBundle\Entity\Contest',
            'validation_groups' => array('general'),
            'strict_edit' => false,
        ));

        $resolver->setAllowedTypes(array('strict_edit' => 'bool'));
    }

    public function getName()
    {
        return 'incenteev_contest';
    }
}
