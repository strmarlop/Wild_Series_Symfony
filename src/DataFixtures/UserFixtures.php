<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
// use Symfony\Component\Security\Core\Security;

class UserFixtures extends Fixture
{
    public const USERS = [
        ['email' => 'contributor@monsite.com', 'roles' => ['ROLE_CONTRIBUTOR'], 'password' => 'contributorpassword'],
        ['email' => 'admin@monsite.com', 'roles' => ['ROLE_ADMIN'], 'password' => 'adminpassword'],
    ];

    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher,)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::USERS as $key => $userInformation){
            $user = new User();
            $user -> setEmail($userInformation['email']);
            $user -> setRoles($userInformation['roles']);

            $hashedPassword = $this->passwordHasher->hashPassword($user, $userInformation['password']);
            $user -> setPassword($hashedPassword);

            $manager -> persist($user);
            $this->addReference('user_' . $userInformation['email'], $user);
        }
        
        $manager->flush();
    }
}
