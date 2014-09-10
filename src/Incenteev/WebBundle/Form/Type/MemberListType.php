<?php

namespace Incenteev\WebBundle\Form\Type;

use Incenteev\WebBundle\Form\DataTransformer\ArrayToLinesTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MemberListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new ArrayToLinesTransformer());
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['list_route'] = $options['list_route'];
        $view->vars['list_route_params'] = $options['list_route_params'];
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'list_route' => 'user_list',
            'list_route_params' => array(),
        ));
        $resolver->setAllowedTypes(array(
            'list_route_params' => 'array',
        ));
    }

    public function getParent()
    {
        return 'textarea';
    }

    public function getName()
    {
        return 'incenteev_member_list';
    }
}
