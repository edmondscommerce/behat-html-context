<?php declare(strict_types=1);

namespace EdmondsCommerce\BehatHtmlContext;

use PHPUnit\Framework\TestCase;
use Behat\Mink\Driver\GoutteDriver;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use GuzzleHttp\Client;

abstract class AbstractTestCase extends TestCase
{
    /**
     * @var Session
     */
    protected $minkSession;

    public function setUp()
    {
        parent::setUp();
        $this->setUpMink();
    }

    protected function setUpMink()
    {
        $guzzle       = new Client();
        $goutteClient = new \Goutte\Client();
        $goutteClient->setClient($guzzle);
        $goutteDriver      = new GoutteDriver($goutteClient);
        $this->minkSession = new Session($goutteDriver);
    }
}