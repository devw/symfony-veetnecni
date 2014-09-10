<?php

namespace Incenteev\WebBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BuiltinBackgroundChoiceType extends AbstractType
{
    private static $builtinBackgrounds = array(
        'background/Snowflakes.jpg' => 'contest.background.snowflakes',
        'background/Stars.jpg' => 'contest.background.stars',
        'background/Purple-stars.jpg' => 'contest.background.purple_stars',
        'background/Circus.jpg' => 'contest.background.circus',
        'background/Grass.jpg' => 'contest.background.grass',
        'background/Color-lines.jpg' => 'contest.background.color_lines',
        'background/Christmas.png' => 'contest.background.christmas',
        'background/Universe.jpg' => 'contest.background.universe',
        'background/Tropical-beach.jpg' => 'contest.background.tropical_beach',
        'background/Camels-in-desert.jpg' => 'contest.background.camels_in_desert',
        'background/Ski.jpg' => 'contest.background.ski',
        'background/Pontoon-blue-sea.jpg' => 'contest.background.pontoon_blue_sea',
        'background/Golf.jpg' => 'contest.background.golf',
        'background/Snowboarder.jpg' => 'contest.background.snowboarder',
        'background/Cooking.jpg' => 'contest.background.cooking',
        'background/Spices.jpg' => 'contest.background.spices',
        'background/Partnership.jpg' => 'contest.background.partnership',
        'background/Race.jpg' => 'contest.background.race',
        'background/Speedometer.jpg' => 'contest.background.speedometer',
        'background/Stopwatch.jpg' => 'contest.background.stopwatch',
    );

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => self::$builtinBackgrounds,
            'expanded' => true,
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'incenteev_builtin_background_choice';
    }
}
