<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserData implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function createUser($userName, $role)
    {
        $user = new User;
        $user->setName($userName);
        $user->setUsername($userName);
        $user->setEmail($userName . '@streetwise.com');
        $user->setEnabled(TRUE);
        $user->addRole('ROLE_' . $role);

        $encoder = $this->container->get('security.password_encoder');
        $password = $encoder->encodePassword($user, $userName);
        $user->setPassword($password);

        return $user;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $user1 = $this->createUser('admin', 'ADMIN');
        $manager->persist($user1);

        $user2 = $this->createUser('lector', 'LECTOR');
        $manager->persist($user2);

        $user3 = $this->createUser('student', 'USER');
        $manager->persist($user3);

        $manager->flush();
    }
}
