<?php

namespace Incenteev\WebBundle\Validator;

use Incenteev\WebBundle\Entity\Prize;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class DistinctRanksValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        $foundRanks = array();

        foreach ($value as $key => $prize) {
            if (!$prize instanceof Prize) {
                continue;
            }
            /** @var $prize Prize  */

            $rank = $prize->getRank();

            if (!in_array($rank, $foundRanks)) {
                $foundRanks[] = $rank;

                continue;
            }

            $this->context->addViolationAtSubPath(sprintf('[%s].rank', $key), $constraint->message, array(), $rank);
        }
    }
}
