<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\TestBaseWeb;

class AdminControllerTest extends TestBaseWeb
{
    public function testAdmin()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/admin/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Error testAdmin");

        $form = $crawler->filter('button#search')->form([
            'search' => 'Product10'],
            'GET');
        $crawler = $this->client->submit($form);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Error admin product search");
        $this->assertEquals(1, $crawler->filter('tbody tr')->count(), "Error admin product search content");

        $crawler = $this->client->request('GET', '/admin/products');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Error admin products");
        $this->assertEquals(10, $crawler->filter('tbody tr')->count(), "Error admin products content");

        $crawler = $this->client->request('GET', '/admin/products');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Error admin products");
        $this->assertEquals(10, $crawler->filter('tbody tr')->count(), "Error admin products content");

        $this->client->request('GET', '/admin/product');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Error admin product add");

        $this->client->request('GET', '/admin/product/edit/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Error admin product edit");

        $this->client->request('GET', '/admin/categories');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Error admin categories");

        $this->client->request('GET', '/admin/category');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Error admin category add");

        $this->client->request('GET', '/admin/category/edit/8');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Error admin category edit");

        $crawler = $this->client->request('GET', '/admin/products/category/knighi');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Error admin category products");
        $this->assertEquals(10, $crawler->filter('tbody tr')->count(), "Error admin category products content");

        $crawler = $this->client->request('GET', '/admin/attributes');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Error admin attributes");
        $this->assertEquals(5, $crawler->filter('tbody tr')->count(), "Error admin attributes content");

        $this->client->request('GET', '/admin/attribute');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Error admin attribute add");

        $this->client->request('GET', '/admin/attribute/edit/1');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Error admin attribute edit");

//        $this->client->request('GET', '/admin/user');
//        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Error admin user");

//        $this->client->request('GET', '/admin/invoice');
//        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Error admin invoice");

        $this->client->request('GET', '/admin/review/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Error admin review");

//        $this->client->request('GET', '/admin/private-message');
//        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Error admin private-message");

        $this->client->request('GET', '/admin/newsletter/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Error admin newsletter");

        $this->client->request('GET', '/admin/newsletter/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), "Error admin newsletter add");
    }
}
