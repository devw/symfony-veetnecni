<?php

namespace Incenteev\WebBundle\Tests\Util;

use Incenteev\WebBundle\Entity\User;
use Incenteev\WebBundle\Util\NameGuesser;

class NameGuesserTest extends \PHPUnit_Framework_TestCase
{
    public function testGuesserSkipKnownFirstNames()
    {
        $user = new User();
        $user->setFirstName('foobar')
            ->setEmail('christophe.coevoet@logsafe.org');

        $guesser = new NameGuesser();

        $guesser->guessNames($user);

        $this->assertEquals('Foobar', $user->getFirstName());
        $this->assertNull($user->getLastName());
    }

    public function testGuesserSkipKnownLastNames()
    {
        $user = new User();
        $user->setLastName('foo')
            ->setEmail('christophe.coevoet@logsafe.org');

        $guesser = new NameGuesser();

        $guesser->guessNames($user);

        $this->assertNull($user->getFirstName());
        $this->assertEquals('Foo', $user->getLastName());
    }

    public function testGuesserSkipUndefinedEmails()
    {
        $user = new User();

        $guesser = new NameGuesser();

        $guesser->guessNames($user);

        $this->assertNull($user->getFirstName());
        $this->assertNull($user->getLastName());
    }

    public function testGuesserSkipInvalidEmails()
    {
        $user = new User();
        $user->setEmail('christophe.coevoet');

        $guesser = new NameGuesser();

        $guesser->guessNames($user);

        $this->assertNull($user->getFirstName());
        $this->assertNull($user->getLastName());
    }

    public function testGuess()
    {
        $user = new User();
        $user->setEmail('christophe.coevoet@logsafe.org');

        $guesser = new NameGuesser();

        $guesser->guessNames($user);

        $this->assertEquals('Christophe', $user->getFirstName());
        $this->assertEquals('Coevoet', $user->getLastName());
    }

    public function testGuessWithoutLastName()
    {
        $user = new User();
        $user->setEmail('christophe@high5now.com');

        $guesser = new NameGuesser();

        $guesser->guessNames($user);

        $this->assertEquals('Christophe', $user->getFirstName());
        $this->assertSame('', $user->getLastName());
    }

    public function testGuessWithMoreWords()
    {
        $user = new User();
        $user->setEmail('christophe.coevoet.test@high5now.com');

        $guesser = new NameGuesser();

        $guesser->guessNames($user);

        $this->assertEquals('Christophe', $user->getFirstName());
        $this->assertEquals('Coevoet Test', $user->getLastName());
    }
}
