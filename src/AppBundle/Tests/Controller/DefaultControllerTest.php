<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\TestBaseWeb;

class DefaultControllerTest extends TestBaseWeb
{
    public function testFrontendPages()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Latest products', $crawler->filter('.latest_products h2')->text());

        $crawler = $client->request('GET', '/products/category/knighi');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Книги', $crawler->filter('.features_items h2')->text());

        $form = $crawler->filter('button#form_filter')->form([], 'GET');
        $crawler = $client->submit($form);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(6, $crawler->filter('.single-products')->count());

        $crawler = $client->request('GET', '/product/voluptates_et_eaque');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Voluptates et eaque.', $crawler->filter('.product-information h2')->text());

        $crawler = $client->request('GET', '/product/voluptates_et_eaque/reviews');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Voluptates et eaque.', $crawler->filter('.product-information h2')->text());

        $crawler = $client->request('GET', '/cart');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Your shopping cart is empty.', $crawler->filter('.register-req p')->text());

        $crawler = $client->request('GET', '/checkout');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Your shopping cart is empty.', $crawler->filter('.register-req p')->text());

        $crawler = $client->request('GET', '/login');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $form = $crawler->filter('button#form_search')->form([
            'form[input]' => 'Voluptates et eaque.'],
            'GET');
        $crawler = $client->submit($form);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('.single-products')->count());
    }
}
