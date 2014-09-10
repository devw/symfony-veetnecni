<?php

namespace Incenteev\WebBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SubmitDataType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $typeOptions = function (Options $options) {
            return array(
                'unit' => $options['unit'],
                'interval' => $options['interval'],
                'required' => false,
            );
        };

        $resolver->setDefaults(array(
            'unit' => null,
            'interval' => null,
            'type' => 'incenteev_data_entry',
            'options' => $typeOptions,
        ));
    }

    public function getParent()
    {
        return 'collection';
    }

    public function getName()
    {
        return 'incenteev_submit_data';
    }
}
