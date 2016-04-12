<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Tests\TestBaseWeb;

class DefaultControllerTest extends TestBaseWeb
{
    public function testFrontendPages()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Error on index");
        $this->assertContains('Latest products', $crawler->filter('.latest_products h2')->text(), "Error on index content");

        $crawler = $client->request('GET', '/products/category/knighi');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Error on category");
        $this->assertContains('Книги', $crawler->filter('.features_items h2')->text(), "Error on category content");

        $form = $crawler->filter('button#form_filter')->form([], 'GET');
        $crawler = $client->submit($form);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Error on filter");
        $this->assertEquals(9, $crawler->filter('.single-products')->count(), "Error on filter content");

        $crawler = $client->request('GET', '/product/product1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Error on product");
        $this->assertContains('Product1', $crawler->filter('.product-information h2')->text(), "Error on product content");

        $crawler = $client->request('GET', '/product/product1/reviews');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Error on review");
        $this->assertContains('Product1', $crawler->filter('.product-information h2')->text(), "Error on review content");

        $crawler = $client->request('GET', '/cart');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Error on cart");
        $this->assertContains('Your shopping cart is empty.', $crawler->filter('.register-req p')->text(), "Error on cart content");

        $crawler = $client->request('GET', '/checkout');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Error on checkout");
        $this->assertContains('Your shopping cart is empty.', $crawler->filter('.register-req p')->text(), "Error on checkout content");

        $crawler = $client->request('GET', '/login');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Error on login page");

        $form = $crawler->filter('button#form_search')->form([
            'form[input]' => 'Product10'],
            'GET');
        $crawler = $client->submit($form);
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Error on search");
        $this->assertEquals(1, $crawler->filter('.single-products')->count(), "Error on search content");
    }
}
