<?php

namespace Incenteev\WebBundle\Form\Type;

use Incenteev\WebBundle\Doctrine\Repository\ParticipationRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContestTeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var $contest \Incenteev\WebBundle\Entity\Contest */
        $contest = $options['contest'];
        $contestId = $contest->getId();

        $builder
            ->add('name', 'text', array(
                'label' => 'contest_team.label.name',
            ))
            ->add('participations', 'entity', array(
                'label' => 'contest_team.label.participations',
                'by_reference' => false,
                'multiple' => true,
                'class' => 'WebBundle:Participation',
                'query_builder' => function (ParticipationRepository $repo) use ($contestId) {
                    return $repo->getContestParticipationsQueryBuilder($contestId);
                },
                'property' => 'user.name',
            ))
            ->add('avatar', 'incenteev_avatar', array(
                'label' => 'prize.label.avatar',
                'mapped' => true,
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $emptyData = function (Options $options, $value) {
            $contest = $options['contest'];

            return function (FormInterface $form) use ($contest, $value) {
                $data = call_user_func($value, $form);

                if (null !== $data) {
                    /** @var $data \Incenteev\WebBundle\Entity\ContestTeam */
                    $data->setContest($contest);
                }

                return $data;
            };
        };

        $resolver->setDefaults(array(
            'data_class' => 'Incenteev\WebBundle\Entity\ContestTeam',
            'empty_data' => $emptyData,
        ));
        $resolver->setRequired(array('contest'));
        $resolver->addAllowedTypes(array('contest' => 'Incenteev\WebBundle\Entity\Contest'));
    }

    public function getName()
    {
        return 'incenteev_contest_team';
    }
}
