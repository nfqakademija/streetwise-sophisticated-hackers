<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use AppBundle\Entity\Lecture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadLectureData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    const LECTURECOUNT = 10;

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        for ($i = 0; $i < $this::LECTURECOUNT; $i++) {
            $lecture = new Lecture();
            $lecture->setTitle($faker->sentence($nbWords = rand(1, 3), $variableNbWords = true))
                ->setDescription($faker->paragraph($nbSentences = rand(1, 5), $variableNbSentences = true))
                ->setDate($faker->dateTimeInInterval($startDate = '-5 years', $interval = '+ 5 years'))
                ->setLecturer($this->getReference('lector-user'))
            ;
            $manager->persist($lecture);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        // the order in which fixtures will be loaded
        // the lower the number, the sooner that this fixture is loaded
        return 2;
    }
}
