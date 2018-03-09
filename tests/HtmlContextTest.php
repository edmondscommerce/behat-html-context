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

    /**
     * @throws \Exception
     */
    public function setUp()
    {
        parent::setUp();

        //Set up the mock server
        $this->server = new MockServer(__DIR__ . '/assets/routers/clickontext.php', $this->getContainerIp(), 8080);
        $this->server->startServer();

        $mink = new Mink(['selenium2' => $this->minkSession]);
        $mink->setDefaultSessionName('selenium2');
        $this->minkSession->start();

        //Set up Mink in the class
        $this->context = new HTMLContext();
        $this->context->setMink($mink);
    }

    public function tearDown()
    {
        parent::tearDown();
        $this->minkSession->stop();
        $this->server->stopServer();
    }

    protected function getUrl(string $uri)
    {
        return sprintf('http://%s%s', $this->containerIp, $uri);
    }

    public function testClickOnTextWillFindTheTextAndClick()
    {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);
        $this->context->iClickOnTheText('Another text');

        $this->assertEquals('Success', $this->minkSession->getPage()->getText());
    }

    public function testClickOnTextWillFailWhenTextIsNotPresent()
    {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);

        $text = "Non existant text";

        $this->expectException(\UnexpectedValueException::class);

        $this->context->iClickOnTheText($text);
    }

    public function testClickOnFirstTextWillClickOnlyTheFirstText()
    {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);

        $text = "First Visible Text";

        $this->context->iClickOnTheFirstVisibleText($text);

        $this->assertEquals('First Text', $this->minkSession->getPage()->getText());

    }

    public function testClickOnFirstVisibleTextThatIsNotPresent()
    {
        $url = $this->server->getUrl('/not-present');

        $this->minkSession->visit($url);

        $text = "First Visible Text";

        $this->expectExceptionMessage(sprintf('Cannot find text: "%s"', $text));

        $this->context->iClickOnTheFirstVisibleText($text);
    }

    public function testClickOnFirstVisibleTextThatIsNotVisible()
    {
        $url = $this->server->getUrl('/invisible');

        $this->minkSession->visit($url);

        $text = "First Visible Text";

        $this->expectExceptionMessage(sprintf('Cannot find text that is visible: "%s"', $text));

        $this->context->iClickOnTheFirstVisibleText($text);
    }

    public function testClickOnTheElementThatIsNotPresent()
    {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);

        $css = '#non-existant-element-id';

        $this->expectException(\UnexpectedValueException::class);

        $this->context->iClickOnTheElement($css);
    }

    public function testClickOnTheElementWillFindTheElementAndClick()
    {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);

        $css = '#success';

        $this->context->iClickOnTheElement($css);

        $this->assertEquals('Success', $this->minkSession->getPage()->getText());
    }

    public function testScrollToElementWillScrollToElement()
    {
//        $url = $this->server->getUrl('/scroll-test');
//
//        $this->minkSession->visit($url);
//
//        $css = '#success';
//
//        $this->context->iScrollTo($css);
//
//        $currentVerticalOffset = $this->minkSession->evaluateScript("return window.pageYOffset;");
    }

    public function testHideElementWillBeHidden()
    {
        $url = $this->server->getUrl('/scroll-test');

        $this->minkSession->visit($url);

        $css = '#success';

        $this->context->iHideElement($css);

        $visible = $this->minkSession->evaluateScript(
            "return document.querySelector('". $css . "').style.display;"
        );

        $this->assertEquals('none', $visible);
    }


}