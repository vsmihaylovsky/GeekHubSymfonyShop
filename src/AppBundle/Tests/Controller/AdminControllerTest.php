<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\TestBaseWeb;

class AdminControllerTest extends TestBaseWeb
{
    public function testAdmin()
    {
        $this->logIn();
        $this->client->request('GET', '/admin');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Error testAdmin");
    }
}
