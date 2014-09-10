<?php

namespace Incenteev\WebBundle\DataFixtures\ORM;

use Incenteev\WebBundle\Entity\Organization;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

/**
 * Workspace Fixtures
 */
class OrganizationFixtures extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        $organization = new Organization();
        $organization->setName($faker->company);
        $organization->setLanguage('en');
        $manager->persist($organization);

        $organization2 = new Organization();
        $organization2->setName($faker->company);
        $organization2->setLanguage('fr');
        $manager->persist($organization2);

        $this->addReference('organization', $organization);
        $this->addReference('organization2', $organization2);

        $manager->flush();
    }
}
