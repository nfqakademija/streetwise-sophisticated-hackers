<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use AppBundle\Entity\Homework;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadHomeworkData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    const HOMEWORKCOUNT = 10;

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        for ($i = 0; $i < $this::HOMEWORKCOUNT; $i++) {
            $homework = new Homework();
            $homework->setTitle($faker->sentence($nbWords = rand(3, 5), $variableNbWords = true))
                ->setDescription($faker->paragraph($nbSentences = rand(3, 7), $variableNbSentences = true))
                ->setDueDate($faker->dateTimeInInterval($startDate = '-4 years', $interval = '+ 5 years'))
                ->setLecturer($this->getReference('lector-user'))
                ;
            $manager->persist($homework);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}
