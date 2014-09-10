<?php

namespace Incenteev\WebBundle\Form\Type;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PrizeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $collapsedForm = $builder->create('details', 'form', array(
                'virtual' => true,
                'label' => false,
                'data_class' => 'Incenteev\WebBundle\Entity\Prize',
                'attr' => array('class' => 'collapse')
            ))
            ->add('description', 'textarea', array(
                'label' => 'prize.label.description',
                'attr' => array(
                    'class' => 'form-prize-item-description',
                ),
                'required' => false,
            ))
            ->add('avatar', 'incenteev_avatar', array(
                'label' => 'prize.label.avatar',
                'mapped' => true,
                'validation_groups' => $options['validation_groups'],
            ))
            // TODO check if the avatar is defined
            ->add('remove_avatar', 'checkbox', array(
                'required' => false,
                'mapped' => false,
            ));

        $builder
            ->add('name', 'text', array(
                'label' => 'prize.label.name',
                'attr' => array(
                    'class' => 'form-prize-item-name',
                ),
            ))
            ->add($collapsedForm)
            ->add('rank', 'hidden', array('attr' => array('class' => 'form-prize-item-rank')))
        ;
        $builder->addEventListener(FormEvents::PRE_BIND, function (FormEvent $event) {
            $data = $event->getData();

            if (empty($data['details']['description']) && empty($data['name']) && empty($data['details']['avatar'])) {
                $data['rank'] = '';
            }

            $event->setData($data);
        }, 50);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $emptyData = function (Options $options, $value) {
            $contest = $options['contest'];

            return function (FormInterface $form) use ($contest, $value) {
                $data = call_user_func($value, $form);

                if (null !== $data) {
                    /** @var $data \Incenteev\WebBundle\Entity\Prize */
                    $data->setContest($contest);
                }

                return $data;
            };
        };

        $resolver->setDefaults(array(
            'data_class' => 'Incenteev\WebBundle\Entity\Prize',
            'empty_data' => $emptyData,
        ));
        $resolver->setRequired(array('contest'));
        $resolver->addAllowedTypes(array('contest' => 'Incenteev\WebBundle\Entity\Contest'));
    }

    public function getName()
    {
        return 'incenteev_prize';
    }
}
