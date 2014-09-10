<?php

namespace Incenteev\WebBundle\Form\Type;

use Incenteev\WebBundle\Doctrine\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContestInvitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $orgId = $options['organization_id'];

        $emails = $builder->create('invitedEmails', 'incenteev_member_list', array(
            'label' => 'invitation.label.enter_emails',
            'required' => false,
            'attr' => array(
                'placeholder' => 'invitation.placeholder.enter_emails',
                'help_text' => 'invitation.help.invited_emails',
            ),
        ));

        $builder
            ->add($emails)
            ->add('owners', 'entity', array(
                'label' => 'invitation.label.owners',
                'property_path' => 'contest.owners',
                'by_reference' => false,
                'multiple' => true,
                'class' => 'WebBundle:User',
                'attr' => array('data-tid' => 'chosen'),
                'query_builder' => function (UserRepository $repo) use ($orgId) {
                    return $repo->getMembersQueryBuilder($orgId);
                },
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Incenteev\WebBundle\Form\Model\ContestInvitation'
        ));
        $resolver->setRequired(array('organization_id'));
    }

    public function getName()
    {
        return 'incenteev_contest_invitation';
    }
}
