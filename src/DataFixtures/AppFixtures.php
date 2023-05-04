<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Pin;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher){
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        //Création d'un utilisateur
        $user = new User();
        $user->setFirstName('Henri');
        $user->setLastName('Zotto');
        $user->setEmail('henri.zotto@example.com');
        $password = $this->hasher->hashPassword($user, 'password1982');
        $user->setPassword($password);
        $manager->persist($user);

        //Création d'une série de pins
        for($i=0; $i<100; $i++){
            $pin = new Pin();
            $pin->setTitle('Titre '.$i);
            $pin->setDescription('Description '.$i);
            $pin->setUser($user);
            $manager->persist($pin);
        }

        $manager->flush();
    }
}
