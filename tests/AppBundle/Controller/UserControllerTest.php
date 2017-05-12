<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AppBundle\Helper\LogInHelper;

/**
 * Class UserControllerTest
 * @package AppBundle\Tests\Controller
 */
class UserControllerTest extends WebTestCase
{
    /**
     * Tests going to users list and clicking link
     */
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        LogInHelper::logInAdmin($client);

        // Go to the list view
        $crawler = $client->request('GET', '/user/');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for GET /user/"
        );

        // Go to the show view
        $crawler = $client->click($crawler->filter('tbody a')->first()->link());
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code"
        );
    }
}

