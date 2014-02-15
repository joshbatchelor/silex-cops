<?php

namespace Cops\Tests\Controller;

use Silex\WebTestCase;

class AuthorControllerTest extends WebTestCase
{
    public function createApplication()
    {
        return require __DIR__.'/../application.php';
    }

    public function testDetailPageOk()
    {
        $client = $this->createClient();

        $crawler = $client->request('GET', '/fr/author/12313');
        $this->assertTrue($client->getResponse()->isOk());

        $crawler = $client->request('GET', '/fr/author/4');
        $this->assertTrue($client->getResponse()->isOk());

        $crawler = $client->request('GET', '/fr/author/list/A');
        $this->assertTrue($client->getResponse()->isOk());

        $crawler = $client->request('GET', '/fr/author/list/0');
        $this->assertTrue($client->getResponse()->isOk());

    }

    public function testDownloadOk()
    {
        $client = $this->createClient();
        $client->request('GET', '/fr/author/4/download/zip');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\BinaryFileResponse', $client->getResponse());

        $client->request('GET', '/fr/author/4/download/tar.gz');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\BinaryFileResponse', $client->getResponse());

    }

    public function testDownloadKo()
    {
        $client = $this->createClient();

        // Redirect to homepage
        $client->request('GET', '/fr/author/13113112/download/zip');
        $this->assertTrue($client->getResponse()->isOk());

        // Redirect to homepage
        $client->request('GET', '/fr/author/1/download/zip');
        $this->assertTrue($client->getResponse()->isOk());

        $client->request('GET', '/fr/author/4/download/dummy');
        $this->assertTrue($client->getResponse()->isOk());
    }
}