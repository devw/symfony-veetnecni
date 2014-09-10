<?php

namespace Incenteev\WebBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class NullToDefaultTextTransformer implements DataTransformerInterface
{
    private $defaultText;

    public function __construct($defaultText)
    {
        $this->defaultText = $this->fixValue(trim($defaultText));
    }

    /**
     * {@inheritDoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return $this->defaultText;
        }

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return $value;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $fixedValue = $this->fixValue($value);

        if ($this->defaultText === $fixedValue) {
            return null;
        }

        return $fixedValue;
    }

    private function fixValue($value)
    {
        return strtr($value, array("\r\n" => "\n", "\r" => "\n"));
    }
}
