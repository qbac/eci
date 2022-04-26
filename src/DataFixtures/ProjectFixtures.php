<?php

namespace App\DataFixtures;

use App\Entity\Project;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProjectFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $project = new Project;
        $project->setName('Spawalnia');
        $element = '2022-01-01';
        $date = \DateTime::createFromFormat('Y-m-d', $element);
        $project->setDateStart($date);
        $element = '2022-12-30';
        $date = \DateTime::createFromFormat('Y-m-d', $element);
        $project->setDateEnd($date);
        $project->setActive(TRUE);
        $manager->persist($project);

        $project = new Project;
        $project->setName('SPA Inowrocaław');
        $element = '2022-01-01';
        $date = \DateTime::createFromFormat('Y-m-d', $element);
        $project->setDateStart($date);
        $element = '2022-12-30';
        $date = \DateTime::createFromFormat('Y-m-d', $element);
        $project->setDateEnd($date);
        $project->setActive(TRUE);
        $manager->persist($project);

        $manager->flush();
    }
}
