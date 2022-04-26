<?php

namespace App\DataFixtures;

use App\Entity\Employ;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class EmployFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $employ = new Employ();
        $employ->setName('Zatrudniony Elbitech');
        $manager->persist($employ);

        $employ = new Employ();
        $employ->setName('Samozatrudnienie');
        $manager->persist($employ);

        $employ = new Employ();
        $employ->setName('Zatrudnienie Zew.');
        $manager->persist($employ);

        $manager->flush();
    }
}
