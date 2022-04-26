<?php

namespace App\DataFixtures;

use App\Entity\Employ;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
//use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
class UserFixtures extends Fixture
{
    private $passwordEncoder;

    //public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $user = new User();
        $user->setEmail('admin@elbitech.pl');
        $user->setActive(true);
        $user->setEmploy(null);
        $user->setFirstName('Admin');
        $user->setLastName('A');
        $user->setRoles(['ROLE_USER','ROLE_ADMIN']);
        $user->setPassword($this->passwordEncoder->hashPassword($user, 'admin'));
        $manager->persist($user);

        $user = new User();
        $user->setEmail('admin1@elbitech.pl');
        $user->setActive(true);
        $user->setEmploy(null);
        $user->setFirstName('Jakusz');
        $user->setLastName('Kólka');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordEncoder->hashPassword($user, 'janusz'));
        $manager->persist($user);

        $user = new User();
        $user->setEmail('admin2@elbitech.pl');
        $user->setActive(true);
        $user->setEmploy(null);
        $user->setFirstName('Aleks');
        $user->setLastName('Pankowski');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordEncoder->hashPassword($user, 'aleks'));
        $manager->persist($user);

        $manager->flush();
    }
}
