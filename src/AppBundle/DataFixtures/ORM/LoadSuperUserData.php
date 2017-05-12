<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

class LoadSuperUserData extends AbstractFixture implements
    FixtureInterface,
    ContainerAwareInterface,
    OrderedFixtureInterface
{
    use ContainerAwareTrait;

    public function createUser($userName, $role): User
    {
        $user = new User;
        $user->setName($userName);
        $user->setUsername($userName);
        $user->setEmail($userName . '@streetwise.com');
        $user->setEnabled(true);
        $user->addRole('ROLE_' . $role);

        $encoder = $this->container->get('security.password_encoder');
        $password = $encoder->encodePassword($user, $userName);
        $user->setPassword($password);

        return $user;
    }

    public function load(ObjectManager $manager)
    {
        $userAdmin = $this->createUser('super_admin', 'SUPER_ADMIN');
        $manager->persist($userAdmin);
        $manager->flush();
    }

    public function getOrder()
    {
        // the order in which fixtures will be loaded
        // the lower the number, the sooner that this fixture is loaded
        return 1;
    }
}
