<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setName('Usuario teste 1');
        $user->setAge(21);
        $user->setCity('NaoMeToque');
        $user->setCpf('166.852.790-10');

        $manager->persist($user);
        $manager->flush();
    }
}
