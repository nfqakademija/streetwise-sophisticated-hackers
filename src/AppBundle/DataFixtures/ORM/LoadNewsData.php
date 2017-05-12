<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use AppBundle\Entity\News;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadNewsData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    const NEWS_COUNT = 10;

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create();
        $users = $manager->getRepository('AppBundle:User')->findAll();
        for ($i = 0; $i < $this::NEWS_COUNT; $i++) {
            $news = new News();
            $news->setTitle($faker->sentence($nbWords = rand(7, 14), $variableNbWords = true))
                ->setDescription($faker->paragraph($nbSentences = rand(5, 20), $variableNbSentences = true))
                ->setDate($faker->dateTimeInInterval($startDate = '-5 years', $interval = '+ 5 years'))
                ->setAuthor($users[rand(0, count($users)-1)])
                ;
            $manager->persist($news);
        }
        $manager->flush();
    }

    public function getOrder()
    {
        return 4;
    }
}
