<?php

namespace Incenteev\WebBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Incenteev\WebBundle\Form\DataTransformer\NullToDefaultTextTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Translation\TranslatorInterface;

class TranslatedDefaultTextType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addViewTransformer(new NullToDefaultTextTransformer($this->translator->trans($options['default_text_key'])));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'trim' => true,
        ));
        $resolver->setRequired(array('default_text_key'));
    }

    public function getParent()
    {
        return 'textarea';
    }

    public function getName()
    {
        return 'incenteev_translated_default_text';
    }
}
