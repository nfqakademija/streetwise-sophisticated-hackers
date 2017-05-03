<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AppBundle\Helper\LogInHelper;

/**
 * Class NewsControllerTest
 * @package AppBundle\Tests\Controller
 */
class NewsControllerTest extends WebTestCase
{
    /**
     * Tests going to news list and clicking link
     */
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        LogInHelper::logInAdmin($client);

        // Go to the list view
        $crawler = $client->request('GET', '/news/');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for GET /news/"
        );

        // Go to the show view
        $crawler = $client->click($crawler->filter('h3 a')->first()->link());
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code"
        );
    }
}
