<?php

namespace Incenteev\WebBundle\Tests\Form\DataTransformer;

use Incenteev\WebBundle\Form\DataTransformer\NullToDefaultTextTransformer;

class NullToDefaultTextTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideTransformedData
     */
    public function testTransform($defaultText, $input, $expected)
    {
        $transformer = new NullToDefaultTextTransformer($defaultText);

        $this->assertEquals($expected, $transformer->transform($input));
    }

    public function provideTransformedData()
    {
        return array(
            array('foo', null, 'foo'),
            array("foo\n", null, 'foo'),
            array('bar', null, 'bar'),
            array('foo', 'bar', 'bar'),
            array('foo', '', ''),
        );
    }

    /**
     * @dataProvider provideReverseTransformedData
     */
    public function testReverseTransform($defaultText, $input, $expected)
    {
        $transformer = new NullToDefaultTextTransformer($defaultText);

        $this->assertEquals($expected, $transformer->reverseTransform($input));
    }

    public function provideReverseTransformedData()
    {
        return array(
            array('foo', null, null),
            array('foo', '', ''),
            array('foo', 'bar', 'bar'),
            array('bar', 'bar', null),
            array("foo\nbar", "foo\nbar", null),
            array("foo\nbar", "foo\r\nbar", null),
        );
    }

    /**
     * @dataProvider provideInvalidReverseInput
     * @expectedException \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function testInvalidReverseTypes($input)
    {
        $transformer = new NullToDefaultTextTransformer('foo');

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
}
