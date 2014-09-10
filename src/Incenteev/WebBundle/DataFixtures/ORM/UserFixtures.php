<?php

namespace Incenteev\WebBundle\DataFixtures\ORM;

use Incenteev\WebBundle\Entity\User;
use Incenteev\WebBundle\Util\NameGuesser;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

/**
 * User Fixtures
 */
class UserFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        /** @var $organization \Incenteev\WebBundle\Entity\Organization */
        $organization = $this->getReference('organization');
        /** @var $organization2 \Incenteev\WebBundle\Entity\Organization */
        $organization2 = $this->getReference('organization2');
        $faker = Factory::create();
        $nameGuesser = new NameGuesser();

        $adminUser = new User();
        $adminUser->setFirstName('admin')
            ->setLastName('with a Vandendriesschish name')
            ->setOrganization($organization)
            ->setEmail('admin@example.org')
            ->setSuperAdmin(true)
            ->setEnabled(true)
            ->setPlainPassword('test');

        $manager->persist($adminUser);
        $this->addReference('user-admin', $adminUser);

        $user = new User();
        $user->setFirstName('normal')
            ->setLastName('user')
            ->setOrganization($organization)
            ->setEmail('user@example.org')
            ->setEnabled(true)
            ->setPlainPassword('logsafe');

        $manager->persist($user);
        $this->addReference('user-normal', $user);

        for ($i = 0; $i < 3; $i++) {
            $user = new User();
            $user->setOrganization($organization)
                ->setEmail($faker->email)
                ->setEnabled(true)
                ->setPlainPassword($faker->word);

            $nameGuesser->guessNames($user);

            // Faker generates lots of emails with a single segment, so let's add a last name in 50% of these cases.
            if ('' === $user->getLastName() && $faker->boolean) {
                $user->setLastName($faker->lastName);
            }

            $manager->persist($user);
            $this->addReference(sprintf('user-%s', $i), $user);
        }

        for ($i = 5; $i < 10; $i++) {
            $user = new User();
            $user->setOrganization($organization2)
                ->setEmail($faker->email)
                ->setEnabled(true)
                ->setPlainPassword($faker->word);

            $nameGuesser->guessNames($user);

            // Faker generates lots of emails with a single segment, so let's add a last name in 50% of these cases.
            if ('' === $user->getLastName() && $faker->boolean) {
                $user->setLastName($faker->lastName);
            }

            $manager->persist($user);
            $this->addReference(sprintf('user-%s', $i), $user);
        }

        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     *
     * @return array
     */
    public function getDependencies()
    {
        return array(__NAMESPACE__ . '\OrganizationFixtures');
    }
}
