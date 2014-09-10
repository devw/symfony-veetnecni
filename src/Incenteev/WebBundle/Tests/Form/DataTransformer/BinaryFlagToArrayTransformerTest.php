<?php

namespace Incenteev\WebBundle\Tests\Form\DataTransformer;

use Incenteev\WebBundle\Form\DataTransformer\BinaryFlagToArrayTransformer;

class BinaryFlagToArrayTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideValidPairs
     */
    public function testTransform(array $flags, $input, $expected)
    {
        $transformer = new BinaryFlagToArrayTransformer($flags);

        $this->assertEquals($expected, $transformer->transform($input));
    }

    public function provideValidPairs()
    {
        return array(
            array(array(1, 2, 4, 8), 1, array(1)),
            array(array(1, 2, 4, 8), 2, array(2)),
            array(array(1, 2, 4, 8), 4, array(4)),
            array(array(1, 2, 4, 8), 8, array(8)),
            array(array(1, 2, 4), 3, array(1, 2)),
            array(array(1, 2, 4, 8, 7), 7, array(1, 2, 4, 7)),
            array(array(1, 2, 4, 8, 7), 10, array(2, 8)),
            array(array(1, 2, 4, 8), null, null),
        );
    }

    /**
     * @dataProvider provideValidReversePairs
     */
    public function testReverseTransform(array $flags, $input, $expected)
    {
        $transformer = new BinaryFlagToArrayTransformer($flags);

        $this->assertEquals($expected, $transformer->reverseTransform($input));
    }

    public function provideValidReversePairs()
    {
        return array(
            array(array(1, 2, 4, 8), array(1), 1),
            array(array(1, 2, 4, 8), array(2), 2),
            array(array(1, 2, 4, 8), array(4), 4),
            array(array(1, 2, 4, 8), array(8), 8),
            array(array(1, 2, 4, 8), array(1, 2), 3),
            array(array(1, 2, 4, 8, 7), array(1, 2, 4, 7), 7),
            array(array(1, 2, 4, 8, 7), array(2, 8), 10),
            array(array(1, 2, 4, 8), null, null),
        );
    }

    /**
     * @dataProvider provideInvalidInput
     * @expectedException \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function testInvalidTypes($input)
    {
        $transformer = new BinaryFlagToArrayTransformer(array(1, 2));

        $transformer->transform($input);
    }

    public function provideInvalidInput()
    {
        return array(
            'string' => array('4'),
            'array' => array(array(1)),
            'float' =>  array(3.5),
            'object' => array(new \stdClass()),
            'boolean' => array(true),
        );
    }

    /**
     * @dataProvider provideInvalidReverseInput
     * @expectedException \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function testInvalidReverseTypes($input)
    {
        $transformer = new BinaryFlagToArrayTransformer(array(1, 2));

        $transformer->reverseTransform($input);
    }

    public function provideInvalidReverseInput()
    {
        return array(
            'string' => array('4'),
            'integer' => array(1),
            'float' =>  array(3.5),
            'object' => array(new \stdClass()),
            'boolean' => array(true),
        );
    }

    /**
     * @dataProvider provideFailingReverseInput
     * @expectedException \Symfony\Component\Form\Exception\TransformationFailedException
     */
    public function testFailedReverseTransform(array $flags, $input)
    {
        $transformer = new BinaryFlagToArrayTransformer($flags);

        $transformer->reverseTransform($input);
    }

    public function provideFailingReverseInput()
    {
        return array(
            array(array(1, 2, 4, 8), array(5)),
            array(array(1, 2, 8), array(1, 2, 4)),
        );
    }
}
