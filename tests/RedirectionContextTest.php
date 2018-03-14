<?php declare(strict_types=1);

namespace EdmondsCommerce\BehatHtmlContext;

use Behat\Mink\Mink;
use EdmondsCommerce\MockServer\MockServer;
use Behat\Mink\Exception\UnsupportedDriverActionException;


class RedirectionContextTest extends AbstractTestCase {

    /**
     * @var RedirectionContext
     */
    private $context;

    /**
     * @var MockServer
     */
    private $server;

    /**
     * @throws \Exception
     */
    public function setUp()
    {
        parent::setUp();

        //Set up the mock server
        $this->server = new MockServer(__DIR__ . '/assets/routers/clickontext.php', $this->getContainerIp(), 8080);
        $this->server->startServer();

        $mink = new Mink(['goutte' => $this->goutteSession]);
        $mink->setDefaultSessionName('goutte');
        $this->goutteSession->start();

        //Set up Mink in the class
        $this->context = new RedirectionContext();
        $this->context->setMink($mink);
    }

    public function testCanInterceptWillThrowExceptionForUnsupportedDriver() {
        $mink = new Mink(['selenium2' => $this->seleniumSession]);
        $mink->setDefaultSessionName('selenium2');
        $this->seleniumSession->start();

        $context = new RedirectionContext();
        $context->setMink($mink);

        $this->expectException(UnsupportedDriverActionException::class);

        $context->canIntercept();
    }
}