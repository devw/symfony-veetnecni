<?php

namespace Incenteev\WebBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class AvailableEmails extends Constraint
{
    public $message = 'invitation.already_used';

    public function validatedBy()
    {
        return 'incenteev_available_emails';
    }
}
