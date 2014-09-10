<?php

namespace Incenteev\WebBundle\Form\Type;

use Incenteev\WebBundle\Entity\Contest;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContestEmailContentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('invitationText', 'incenteev_translated_default_text', array(
                'label' => 'contest.label.invitation_text',
                'required' => false,
                'help_text' => 'contest.help.invitation_text',
                'default_text_key' => 'mail.invitation.content_html',
            ))
            ->add('reminderText', 'incenteev_translated_default_text', array(
                'label' => 'contest.label.reminder_text',
                'required' => false,
                'help_text' => 'contest.help.reminder_text',
                'default_text_key' => 'mail.dataEntryReminder.content_html',
                'label_attr' => array(
                    'class' => 'form-spaced',
                ),
            ))
            ->add('reminderPeriodicity', 'incenteev_periodicity', array(
                'label' => 'contest.label.reminder_periodicity',
            ))
            ->add('summaryText', 'incenteev_translated_default_text', array(
                'label' => 'contest.label.summary_text',
                'required' => false,
                'help_text' => 'contest.help.summary_text',
                'default_text_key' => 'mail.summary.content',
            ))
            ->add('summaryPeriodicity', 'incenteev_periodicity', array(
                'label' => 'contest.label.summary_periodicity',
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Incenteev\WebBundle\Entity\Contest',
            'validation_groups' => array('email_content'),
        ));
    }

    public function getName()
    {
        return 'incenteev_contest_email_content';
    }
}
