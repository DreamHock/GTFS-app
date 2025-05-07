<?php

namespace App\DataFixtures;

use App\Document\Role;
use App\Document\User;
use Doctrine\Bundle\MongoDBBundle\Fixture\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Create admin role
        $adminRole = new Role();
        $adminRole->setName('ROLE_ADMIN');
        $manager->persist($adminRole);
        
        // Create user role
        $userRole = new Role();
        $userRole->setName('ROLE_USER');
        $manager->persist($userRole);
        
        // Create admin user
        $adminUser = new User();
        $adminUser->setEmail('admin@example.com');
        $adminUser->addRole($adminRole);
        $manager->persist($adminUser);
        
        // Create regular user
        $regularUser = new User();
        $regularUser->setEmail('user@example.com');
        $regularUser->addRole($userRole);
        $manager->persist($regularUser);

        $manager->flush();
    }
}
