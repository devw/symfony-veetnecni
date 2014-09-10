<?php

namespace Incenteev\WebBundle\Form\Type;

use Incenteev\WebBundle\Doctrine\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrganizationTeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $orgId = $options['organization_id'];

        $builder
            ->add('name', 'text', array(
                'label' => 'organization_team.label.name',
            ))
            ->add('avatar', 'incenteev_avatar', array(
                'label' => 'organization_team.label.avatar',
            ))
            ->add('members', 'entity', array(
                'label' => 'organization_team.label.members',
                'multiple' => true,
                'class' => 'WebBundle:User',
                'attr' => array('data-tid' => 'chosen'),
                'query_builder' => function (UserRepository $repo) use ($orgId) {
                    return $repo->getMembersQueryBuilder($orgId);
                },
                'required' => false,
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Incenteev\WebBundle\Entity\OrganizationTeam',
        ));
        $resolver->setRequired(array('organization_id'));
    }

    public function getName()
    {
        return 'incenteev_organization_team';
    }
}
