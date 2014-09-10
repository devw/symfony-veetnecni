<?php

namespace Incenteev\WebBundle\Tests\Form\DataTransformer;

use Incenteev\WebBundle\Form\DataTransformer\FloatToStringTransformer;

class FloatToStringTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideTransformedData
     */
    public function testTransform($input, $expected)
    {
        $transformer = new FloatToStringTransformer();

        $this->assertSame($expected, $transformer->transform($input));
    }

    public function provideTransformedData()
    {
        return array(
            array(null, ''),
            array(2, '2'),
            array(2.0, '2'),
            array(4.3, '4.3'),
            array('5.4', '5.4'),
            array('5.40', '5.4'),
            array('50.40', '50.4'),
            array('50', '50'),
            array('50.000', '50'),
        );
    }

    /**
     * @dataProvider provideReverseTransformedData
     */
    public function testReverseTransform($input, $expected)
    {
        $transformer = new FloatToStringTransformer();

        $this->assertSame($expected, $transformer->reverseTransform($input));
    }

    public function provideReverseTransformedData()
    {
        return array(
            array(null, null),
            array('', null),
            array('1', 1.0),
            array('1.0', 1.0),
            array('1.2', 1.2),
            array('1,2', 1.2),
            array('1.2000', 1.2),
            array('10.2000', 10.2),
            array('10.000', 10.0),
            array('10', 10.0),
            array('5.4a', 5.4),
            array('abc5.4a', 5.4),
            array('e.4a', 0.4),
            array(',4', 0.4),
            array('1,4.5', 1.4),
        );
    }

    /**
     * @dataProvider provideInvalidInput
     * @expectedException \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function testInvalidTypes($input)
    {
        $transformer = new FloatToStringTransformer();

        $transformer->transform($input);
    }

    public function provideInvalidInput()
    {
        return array(
            'array' => array(array(1)),
            'string' => array('abc'),
            'string2' => array('4.5a'),
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
        $transformer = new FloatToStringTransformer();

        $transformer->reverseTransform($input);
    }

    public function provideInvalidReverseInput()
    {
        return array(
            'array' => array(array(1)),
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
    public function testFailedReverseTransform($input)
    {
        $transformer = new FloatToStringTransformer;

        $transformer->reverseTransform($input);
    }

    public function provideFailingReverseInput()
    {
        return array(
            'textual string' => array('abc'),
            'NaN' => array('NaN'),
        );
    }
}
