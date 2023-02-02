<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;


class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // PWD = test1234
        $pwd = '$2y$13$fLmZ8dH5YLggINEg.gtJFOz2x1IjseZvE0sUnRhxNqxs94sJl./NG';

        for ($i=0; $i<10; $i++) {
            $object = (new User())
                ->setEmail("user$i@user.fr")
                ->setPassword($pwd)
                ->setRoles([])
            ;
            $manager->persist($object);
        }

        $object = (new User())
            ->setEmail('admin@user.fr')
            ->setPassword($pwd)
            ->setRoles(["ROLE_ADMIN"])
        ;
        $manager->persist($object);

        $object = (new User())
            ->setEmail('doctor@user.fr')
            ->setPassword($pwd)
            ->setRoles(["ROLE_DOCTOR"])
        ;
        $manager->persist($object);

        $manager->flush();
    }
}
