<?php

namespace Incenteev\WebBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContestTeamCollectionType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $typeOptions = function (Options $options) {
            return array(
                'contest' => $options['contest'],
                'required' => false,
                'label' => false,
            );
        };

        $resolver->setDefaults(array(
            'type' => 'incenteev_contest_team',
            'allow_add' => true,
            'allow_delete' => true,
            'options' => $typeOptions,
            'attr' => array('data-tid' => 'team-widget'),
        ));
        $resolver->setRequired(array('contest'));
        $resolver->addAllowedTypes(array('contest' => 'Incenteev\WebBundle\Entity\Contest'));
    }

    public function getParent()
    {
        return 'collection';
    }

    public function getName()
    {
        return 'incenteev_contest_team_collection';
    }
}
