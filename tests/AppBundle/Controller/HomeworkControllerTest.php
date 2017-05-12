<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AppBundle\Helper\LogInHelper;

/**
 * Class HomeworkControllerTest
 * @package AppBundle\Tests\Controller
 */
class HomeworkControllerTest extends WebTestCase
{
    /**
     * Tests going to homework list and clicking link
     */
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        LogInHelper::logInAdmin($client);

        // Go to the list view
        $crawler = $client->request('GET', '/homework/');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for GET /homework/"
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
