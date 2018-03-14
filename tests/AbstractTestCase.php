<?php declare(strict_types=1);

namespace EdmondsCommerce\BehatHtmlContext;

use Behat\Mink\Driver\Selenium2Driver;
use PHPUnit\Framework\TestCase;
use Behat\Mink\Driver\GoutteDriver;
use Behat\Mink\Session;
use GuzzleHttp\Client;

abstract class AbstractTestCase extends TestCase
{
    /**
     * @var Session
     */
    protected $seleniumSession;

    /**
     * @var Session
     */
    protected $goutteSession;
    protected $containerIp;

    /**
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     * @param bool $headless
     */
    protected function setUpSeleniumMink(bool $headless = true)
    {
        $args = [
            'args' => [
                '--disable-gpu',
                '--window-size=1920,1080',
                '--start-maximised',
            ],
        ];

        if ($headless) {
            $args['args'][] = '--headless';
        }

        $driver = new Selenium2Driver('chrome', $args);

        $this->seleniumSession = new Session($driver);
    }

    public function setUp()
    {
        parent::setUp();
        $this->setUpSeleniumMink();
        $this->setUpGoutteMink();
    }

    protected function setUpGoutteMink()
    {
        $guzzle       = new Client();
        $goutteClient = new \Goutte\Client();
        $goutteClient->setClient($guzzle);
        $goutteDriver      = new GoutteDriver($goutteClient);
        $this->goutteSession = new Session($goutteDriver);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     * @return mixed
     */
    protected function getContainerIp()
    {
        $commandToExecute = 'ip addr show eth0 | grep "inet\b" | awk \'{print $2}\' | cut -d/ -f1';

        exec($commandToExecute, $commandOutput, $exitCode);


        return array_pop($commandOutput);
    }
}
