<?php

namespace Incenteev\WebBundle\Form\Type;

use Incenteev\WebBundle\Entity\Periodicity;
use Incenteev\WebBundle\Form\DataTransformer\BinaryFlagToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PeriodicityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $weeksBuilder = $builder->create('weeks', 'choice', array(
            'label' => 'periodicity.label.weeks',
            'multiple' => true,
            'expanded' => true,
            'choices' => Periodicity::getWeekChoices(),
            'attr' => array(
                'class' => 'periodicity-weeks',
            ),
        ))->addModelTransformer(new BinaryFlagToArrayTransformer(Periodicity::getWeekValues()));

        $daysBuilder = $builder->create('days', 'choice', array(
            'label' => 'periodicity.label.days',
            'multiple' => true,
            'expanded' => true,
            'choices' => Periodicity::getDayChoices(),
            'attr' => array(
                'class' => 'periodicity-days',
            ),
        ))->addModelTransformer(new BinaryFlagToArrayTransformer(Periodicity::getDayValues()));

        $hoursBuilder = $builder->create('hours', 'choice', array(
            'label' => 'periodicity.label.hours',
            'multiple' => true,
            'choices' => Periodicity::getHourChoices(),
            'attr' => array(
                'class' => 'periodicity-hours',
            ),
        ))->addModelTransformer(new BinaryFlagToArrayTransformer(Periodicity::getHourValues()));

        $builder
            ->add($weeksBuilder)
            ->add($daysBuilder)
            ->add($hoursBuilder)
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Incenteev\WebBundle\Entity\Periodicity',
            'attr' => array(
                'class' => 'periodicity well',
            ),
        ));
    }

    public function getName()
    {
        return 'incenteev_periodicity';
    }
}
