<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\AppBundle\Helper\LogInHelper;

/**
 * Class AssignmentControllerTest
 * @package AppBundle\Tests\Controller
 */
class AssignmentControllerTest extends WebTestCase
{
    /**
     * Tests going to homework list, clicking link and going to assignment (as admin)
     */
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        LogInHelper::logInAdmin($client);

        // Go to the list view
        $crawler = $client->request('GET', '/homework/');

        // Go to the show view
        $crawler = $client->click($crawler->filter('tbody a')->first()->link());

        // Go to assignment
        $crawler = $client->click($crawler->selectLink('Show')->link());
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode(),
            "Unexpected HTTP status code"
        );
    }
}
