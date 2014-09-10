<?php

namespace Incenteev\WebBundle\Tests\Entity;

use Incenteev\WebBundle\Entity\User;

class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $firstName
     * @param string $lastName
     * @param string $expectedFirstName
     * @param string $expectedLastName
     *
     * @dataProvider getNormalizedNames
     */
    public function testNormalizeNames($firstName, $lastName, $expectedFirstName, $expectedLastName)
    {
        $user = new User();
        $user->setFirstName($firstName)
            ->setLastName($lastName);

        $this->assertEquals($expectedFirstName, $user->getFirstName());
        $this->assertEquals($expectedLastName, $user->getLastName());
    }

    public function getNormalizedNames()
    {
        return array(
            array('christophe', 'coevoet', 'Christophe', 'Coevoet'),
            array(null, 'christophe.coevoet', null, 'Christophe.coevoet'),
            array('simon-pierre', 'christophe coevoet', 'Simon-Pierre', 'Christophe Coevoet'),
        );
    }
}
