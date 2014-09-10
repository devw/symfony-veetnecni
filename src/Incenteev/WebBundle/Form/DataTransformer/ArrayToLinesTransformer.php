<?php

namespace Incenteev\WebBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class ArrayToLinesTransformer implements DataTransformerInterface
{
    /**
     * {@inheritDoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        return implode("\n", $value);
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return array();
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        return preg_split('#[\s,;]+#', $value, null, PREG_SPLIT_NO_EMPTY);
    }
}
