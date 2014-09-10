<?php

namespace Incenteev\WebBundle\Validator;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AvailableEmailsValidator extends ConstraintValidator
{
    private $registry;
    private $securityContext;

    public function __construct(ManagerRegistry $registry, SecurityContextInterface $securityContext)
    {
        $this->registry = $registry;
        $this->securityContext = $securityContext;
    }

    public function validate($value, Constraint $constraint)
    {
        if (empty($value)) {
            return;
        }

        $emails = array_map(function ($v) { return mb_strtolower($v, 'UTF-8');}, $value);

        /** @var $organization \Incenteev\WebBundle\Entity\Organization */
        $organization = $this->securityContext->getToken()->getUser()->getOrganization();

        $invalidEmails = array_flip($this->getUserRepository()->getEmailAlreadyUsed($emails, $organization));

        foreach ($emails as $key => $email) {
            if (isset($invalidEmails[$email])) {
                $this->context->addViolationAtSubPath($key, $constraint->message, array(), $email);
            }
        }
    }

    /**
     * @return \Incenteev\WebBundle\Doctrine\Repository\UserRepository
     */
    private function getUserRepository()
    {
        return $this->registry->getRepository('WebBundle:User');
    }
}
