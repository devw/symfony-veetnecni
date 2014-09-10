<?php

namespace Incenteev\WebBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class BinaryFlagToArrayTransformer implements DataTransformerInterface
{
    private $flags;

    public function __construct(array $flags)
    {
        $this->flags = $flags;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!is_int($value)) {
            throw new UnexpectedTypeException($value, 'integer');
        }

        $values = array();

        foreach ($this->flags as $flag) {
            if ($flag === ($flag & $value)) {
                $values[] = $flag;
            }
        }

        return $values;
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!is_array($value)) {
            throw new UnexpectedTypeException($value, 'array');
        }

        $result = 0;

        foreach ($value as $flag) {
            if (!in_array($flag, $this->flags)) {
                throw new TransformationFailedException(sprintf('Unsupported flag %s', $flag));
            }

            $result |= $flag;
        }

        return $result;
    }
}
