<?php declare(strict_types=1);

namespace EdmondsCommerce\BehatHtmlContext;

use Behat\Mink\Driver\Selenium2Driver;
use Behat\MinkExtension\ServiceContainer\Driver\Selenium2Factory;
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
    protected $containerIp;

    public function setUp()
    {
        parent::setUp();
        $this->setUpSeleniumMink();
    }

    protected function setUpMink()
    {
        $guzzle       = new Client();
        $goutteClient = new \Goutte\Client();
        $goutteClient->setClient($guzzle);
        $goutteDriver      = new GoutteDriver($goutteClient);
        $this->minkSession = new Session($goutteDriver);

    }

    protected function setUpSeleniumMink(bool $headless = true)
    {
        $args = [
            'args' => [
                '--disable-gpu',
                '--window-size=1920,1080',
                '--start-maximised',
            ],
        ];

        if ($headless)
        {
            $args['args'][] = '--headless';
        }

        $driver = new Selenium2Driver('chrome', $args);

        $this->minkSession = new Session($driver);
    }

    protected function getContainerIp()
    {
        return getHostByName(getHostName());
    }
}