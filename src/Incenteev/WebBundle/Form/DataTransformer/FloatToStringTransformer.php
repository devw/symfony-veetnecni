<?php

namespace Incenteev\WebBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class FloatToStringTransformer implements DataTransformerInterface
{
    /**
     * {@inheritDoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return '';
        }

        if (!is_numeric($value) && !is_string($value)) {
            throw new UnexpectedTypeException($value, 'numeric');
        }

        if (is_string($value) && $this->trimValue($value) !== (string) (float) $value) {
            throw new UnexpectedTypeException($value, 'numeric');
        }

        return (string) (float) $value;
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        $value = trim($value);

        if ('' === $value) {
            return null;
        }

        if (!preg_match('/\d/', $value)) {
            throw new TransformationFailedException(sprintf('"%s" is not a valid number', $value));
        }

        $value = str_replace(',', '.', $value);
        $cleanedValue = preg_replace('/^[^\d\.]+/', '', $value);

        $transformed = (float) $cleanedValue;

        return $transformed;
    }

    private function trimValue($value)
    {
        if (false === strpos($value, '.')) {
            return $value;
        }

        return rtrim(rtrim($value, '0'), '.');
    }
}
