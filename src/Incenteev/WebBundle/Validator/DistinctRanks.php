<?php

namespace Incenteev\WebBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class DistinctRanks extends Constraint
{
    public $message = 'prize.rank_already_used';
}
