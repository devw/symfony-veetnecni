<?php

namespace Incenteev\WebBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DataEntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', 'date', array(
                'disabled' => true,
                'widget' => 'single_text',
                'format' => \IntlDateFormatter::FULL,
            ))
            ->add('value', 'incenteev_unit_number', array(
                'unit' => $options['unit'],
                'attr' => array(
                    'class' => 'data-item',
                ),
            ))
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['interval'] = $options['interval'];
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Incenteev\WebBundle\Entity\DataEntry',
            'unit' => null,
            'interval' => null,
        ));

        $resolver->setAllowedTypes(array('interval' => array('null', 'DateInterval')));
    }

    public function getName()
    {
        return 'incenteev_data_entry';
    }
}
