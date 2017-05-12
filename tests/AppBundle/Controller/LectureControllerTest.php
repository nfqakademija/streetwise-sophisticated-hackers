<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AppBundle\Helper\LogInHelper;

/**
 * Class LectureControllerTest
 * @package AppBundle\Tests\Controller
 */
class LectureControllerTest extends WebTestCase
{
    /**
     * Tests going to lecture list and clicking link
     */
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        LogInHelper::logInAdmin($client);

        // Go to the list view
        $crawler = $client->request('GET', '/lecture/');
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code for GET /lecture/"
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
