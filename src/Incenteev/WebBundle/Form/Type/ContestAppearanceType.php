<?php

namespace Incenteev\WebBundle\Form\Type;

use Incenteev\WebBundle\Entity\Contest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContestAppearanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $background = $builder->create('background', 'form', array(
                'label' => 'contest.label.background',
                'mapped' => false,
            ))
            ->add('builtin', 'incenteev_builtin_background_choice', array(
                'label' => false,
                'required' => false,
                'attr' =>  array(
                    'class' => 'builtin builtin-background',
                ),
            ))
            ->add('new', 'incenteev_avatar', array(
                'label' => 'contest.label.upload_image',
                'validation_groups' => $options['validation_groups'],
                'help_text' => 'contest.help.background',
                'mapped' => true,
            ))
            // TODO check if the avatar is defined
            ->add('remove_background', 'checkbox', array(
                'required' => false,
                'mapped' => false,
                'attr' =>  array(
                    'class' => 'form-remove-background',
                ),
            ));

        $styles = $builder->create('styles', 'form', array(
                'label' => 'contest.label.styles',
                'help_text' => 'contest.help.theme_not_saved',
                'attr' =>  array(
                    'class' => 'form-appearance-customization',
                ),
            ))
            ->add('background_color', 'text', array(
                'label' => 'contest.label.background_color',
                'required' => false,
                'attr' =>  array(
                    'class' => 'color-picker form-appearance-color',
                    'data-target' => 'body',
                ),
            ))
            ->add('background_position', 'choice', array(
                'choices' => Contest::getBackgroundPositionChoices(),
                'label' => 'contest.label.background_position',
                'required' => false,
                'expanded' => true,
                'multiple' => false,
                'attr' =>  array(
                    'class' => 'form-appearance-position',
                ),
            ))
            ->add('background_repeat', 'checkbox', array(
                'label' => 'contest.label.background_repeat',
                'required' => false,
                'attr' =>  array(
                    'class' => 'form-appearance-repeat',
                ),
            ))
            ->add('overlay', 'choice', array(
                'choices' => Contest::getOverlayChoices(),
                'label' => 'contest.label.overlay',
                'required' => false,
                'expanded' => true,
                'multiple' => false,
                'attr' =>  array(
                    'class' => 'form-appearance-overlay',
                ),
            ));

        $builder
            ->add($background)
            ->add($styles)
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Incenteev\WebBundle\Entity\Contest',
            'validation_groups' => array('appearance'),
        ));
    }

    public function getName()
    {
        return 'incenteev_contest_appearance';
    }
}
