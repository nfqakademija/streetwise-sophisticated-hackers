<?php

namespace Tests\AppBundle\Helper;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * Class LogInHelper
 * @package Tests\AppBundle\Helper
 */
class LogInHelper
{
    /**
     * @param Client $client
     */
    public static function logInAdmin(Client $client)
    {
        static::logInUser($client, 'admin@streetwise.com');
    }

    /**
     * @param Client $client
     * @param $email
     */
    public static function logInUser(Client $client, $email)
    {
        $user =
            $client
                ->getContainer()
                ->get('doctrine.orm.default_entity_manager')
                ->getRepository('AppBundle:User')
                ->findOneBy(['email' => $email]);

        static::performUserLogIn($client, $user);
    }

    /**
     * @param Client $client
     * @param $user
     * @param $roles
     */
    public static function performLogIn(Client $client, $user, $roles)
    {
        $session = $client->getContainer()->get('session');

        $firewall = 'main';
        $token = new UsernamePasswordToken($user, null, $firewall, $roles);
        $session->set('_security_' . $firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
    }

    /**
     * @param Client $client
     * @param User $user
     */
    private static function performUserLogIn(Client $client, User $user)
    {
        static::performLogIn($client, $user, $user->getRoles());
    }
}
