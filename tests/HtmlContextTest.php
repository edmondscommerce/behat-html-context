<?php declare(strict_types=1);

namespace EdmondsCommerce\BehatHtmlContext;


use Behat\Mink\Mink;
use EdmondsCommerce\MockServer\MockServer;

class HtmlContextTest extends AbstractTestCase
{
    /**
     * @var HtmlContext
     */
    private $context;

    /**
     * @var MockServer
     */
    private $server;

    public function setUp()
    {
        parent::setUp();

        //Set up the mock server
        $this->server = new MockServer(__DIR__ . '/assets/routers/clickontext.php');
        $this->server->startServer();

        //Set up Mink in the class
        $context = new HTMLContext();
        $context->setMink(new Mink([$this->minkSession]));
    }

    public function testClickOnTextWillFindTheTextAndClick()
    {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);
        $this->context->iClickOnTheText('Another Text');

        $this->assertEquals('Success', $this->minkSession->getPage()->getContent());
    }

    public function testClickOnTextWillFailWhenTextIsNotPresent()
    {

    }

    public function testClickOnFirstTextWillClickOnlyTheFirstText()
    {

    }

    public function testClickOnFirstTextWillNotClickOnOtherTextInstances()
    {

    }
}