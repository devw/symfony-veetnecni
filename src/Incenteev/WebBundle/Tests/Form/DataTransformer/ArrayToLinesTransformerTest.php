<?php

namespace Incenteev\WebBundle\Tests\Form\DataTransformer;

use Incenteev\WebBundle\Form\DataTransformer\ArrayToLinesTransformer;

class ArrayToLinesTransformerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideTransformedData
     */
    public function testTransform($input, $expected)
    {
        $transformer = new ArrayToLinesTransformer();

        $this->assertEquals($expected, $transformer->transform($input));
    }

    public function provideTransformedData()
    {
        return array(
            array(null, null),
            array(array('foo'), 'foo'),
            array(array('foo', 'bar'), "foo\nbar"),
            array(array('foo', 'bar', 'baz'), "foo\nbar\nbaz"),
        );
    }

    /**
     * @dataProvider provideReverseTransformedData
     */
    public function testReverseTransform($input, $expected)
    {
        $transformer = new ArrayToLinesTransformer();

        $this->assertEquals($expected, $transformer->reverseTransform($input));
    }

    public function provideReverseTransformedData()
    {
        return array(
            array(null, array()),
            array('', array()),
            array('foo', array('foo')),
            array("foo\nbar", array('foo', 'bar')),
            array("foo\n\n\r\nbar", array('foo', 'bar')),
            array("foo,\nbar", array('foo', 'bar')),
            array("foo,bar", array('foo', 'bar')),
            array("foo,;,bar", array('foo', 'bar')),
            array("foo   bar", array('foo', 'bar')),
            array("\nfoo\tbar", array('foo', 'bar')),
            array("foo ,\tbar baz ", array('foo', 'bar', 'baz')),
        );
    }

    /**
     * @dataProvider provideInvalidInput
     * @expectedException \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function testInvalidTypes($input)
    {
        $transformer = new ArrayToLinesTransformer();

        $transformer->transform($input);
    }

    public function provideInvalidInput()
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
     * @dataProvider provideInvalidReverseInput
     * @expectedException \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function testInvalidReverseTypes($input)
    {
        $transformer = new ArrayToLinesTransformer();

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
