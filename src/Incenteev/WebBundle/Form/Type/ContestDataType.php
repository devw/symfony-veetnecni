<?php

namespace Incenteev\WebBundle\Form\Type;

use Incenteev\WebBundle\Entity\Contest;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContestDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dataName', 'text', array(
                'required' => false,
                'label' => 'contest.label.data_name',
                'help_text' => 'contest.help.data_name',
            ))
            ->add('unit', 'text', array(
                'required' => false,
                'label' => 'contest.label.unit',
                'help_text' => $options['strict_edit'] ? 'contest.help.disabled_after_launch' : 'contest.help.unit',
                'disabled' => $options['strict_edit'],
            ))
            ->add('granularity', 'choice', array(
                'label' => 'contest.label.granularity',
                'help_text' => $options['strict_edit'] ? 'contest.help.disabled_after_launch' : 'contest.help.granularity',
                'choices' => Contest::getGranularityChoices(),
                'disabled' => $options['strict_edit'],
            ))
            ->add('updatedByParticipants', 'choice', array(
                'label' => 'contest.label.updated_by_participants',
                'expanded' => true,
                'choice_list' => new ChoiceList(array(false, true), array('contest.label.result_update.admins', 'contest.label.result_update.admins_and_user')),
                'help_text' => 'contest.help.updated_by_participants',
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Incenteev\WebBundle\Entity\Contest',
            'validation_groups' => array('data'),
            'strict_edit' => false,
        ));

        $resolver->setAllowedTypes(array('strict_edit' => 'bool'));
    }

    public function getName()
    {
        return 'incenteev_contest_data';
    }
}
