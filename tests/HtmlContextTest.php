<?php declare(strict_types=1);

namespace EdmondsCommerce\BehatHtmlContext;


use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ExpectationException;
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

    public function testScrollToElementWithIncorrectSelectorTypeWillFail() {
        $url = $this->server->getUrl('/scroll-test');

        $this->minkSession->visit($url);

        $selectorType = 'data-id';
        $selector = 'div';

        $this->expectExceptionMessage("Selector type has to be either 'class' or 'id'");

        $this->context->iScrollToElement($selectorType, $selector);
    }

    public function testScrollToElementWithSelectorPrecededWithDotOrHashWillFail() {
        $url = $this->server->getUrl('/scroll-test');

        $this->minkSession->visit($url);

        $selectorType = 'id';
        $selector = '#success';

        $this->expectExceptionMessage("Selector should plain without a . or #");

        $this->context->iScrollToElement($selectorType, $selector);
    }

    public function testScrollToElementWillScrollToTheElement() {
        $url = $this->server->getUrl('/scroll-test');

        $this->minkSession->visit($url);

        $selectorType = 'id';
        $selector = 'success';
        $fullSelector = '#success';

        $script = <<<JS
(function () {
    var el = document.querySelector('$fullSelector');
    var isInViewport = function (elem) {
    var bounding = elem.getBoundingClientRect();
    return (
        bounding.top >= 0 &&
        bounding.left >= 0 &&
        bounding.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        bounding.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
};
        
    return isInViewport(el);
})()
JS;


        $this->context->iScrollToElement($selectorType, $selector);

        $evaluatedScript = $this->minkSession->evaluateScript("return $script");

        $this->assertTrue($evaluatedScript);
    }


    public function testScrollToWillScrollToTheDesiredElement() {
//        $url = $this->server->getUrl('/scroll-test');
//
//        $this->minkSession->visit($url);
//
//        $css = '#success';
//
//        $this->context->iScrollTo($css);
//
//        $script = <<<JS
//    return document.querySelector("$css").offsetTop;
//JS;
//
//        $script2 = <<<JS
//    return window.pageYOffset;
//JS;
//
//
//        $evaluatedScript = $this->minkSession->evaluateScript("$script");
//
//        $this->assertTrue($evaluatedScript);
    }

    public function testMaximiseWindowWillMaximiseWindow() {

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

    public function testMatchElementWillFindTheElement() {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);

        $css = '.nested';

        $this->assertTrue($this->context->iMatchTheElement($css));
    }

    public function testMatchElementWillNotFindAnElement() {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);

        $css = '#some-element-that-doesnt-exist';

        $this->expectException(\UnexpectedValueException::class);

        $this->context->iMatchTheElement($css);

    }

    public function testDontMatchTheElementWillNotFindAnElement() {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);

        $css = '.non-existant-element';

        $this->assertTrue($this->context->iDontMatchTheElement($css));
    }

    public function testDontMatchTheElementMatchesTheElement() {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);

        $css = '.nested';

        $this->expectException(\UnexpectedValueException::class);

        $this->context->iDontMatchTheElement($css);
    }

    public function testShouldSeeXorYShouldFindOneOfTheElementsOnly() {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);

        $css1 = '#success';

        $css2 = '.non-existent-element';

        $this->assertTrue($this->context->iShouldSeeXorY($css1, $css2));
    }

    public function testShouldSeeXorYMatchesNoElements() {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);

        $css1 = '#success';

        $css2 = '.nested';

        $this->expectException(\Exception::class);

        $this->context->iShouldSeeXorY($css1, $css2);
    }

    public function testSubmitTheFormSubmitsTheForm() {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);

        $id = 'successful-form';

        $this->context->iSubmitTheForm($id);

        $this->assertEquals('Success', $this->minkSession->getPage()->getText());
    }

    public function testSelectShouldContainValueWillFindASelectWithSpecifiedOptionValue() {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);

        $selectName = 'test-select';
        $optionValue = '2';

        $this->assertTrue($this->context->theSelectShouldContainValue($selectName, $optionValue));
    }

    public function testSelectShouldContainValueWillNotFindSpecifiedOptionValue() {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);

        $selectName = 'test-select';
        $optionValue = '3';

        $this->expectException(\Exception::class);

        $this->context->theSelectShouldContainValue($selectName, $optionValue);
    }

    public function testSelectShouldNotContainValueWillFindASelectContainingSpecifiedValue() {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);

        $selectName = 'test-select';
        $optionValue = '3';

        $this->assertTrue($this->context->theSelectShouldNotContainValue($selectName, $optionValue));

    }

    public function testSelectShouldNotContainValueWillNotFindASelectContainingSpecifiedValue() {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);

        $selectName = 'test-select';
        $optionValue = '2';

        $this->expectException(\Exception::class);

        $this->context->theSelectShouldNotContainValue($selectName, $optionValue);
    }

    public function testTheElementAttributeShouldNotContainValueWillNotFindAnElement() {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);

        $css = '#successful-form2';
        $attribute = 'method';
        $value = 'POST';

        $this->expectException(\Exception::class);

        $this->context->theElementAttributeShouldNotContainValue($css, $attribute, $value);
    }

    public function testTheElementAttributeShouldNotContainValueWillContainSpecifiedValue() {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);

        $css = '#successful-form';
        $attribute = 'method';
        $value = 'POST';

        $this->expectException(\Exception::class);

        $this->context->theElementAttributeShouldNotContainValue($css, $attribute, $value);
    }

    public function testTheElementAttributeShouldNotContainValueWillNotContainSpecifiedValue() {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);

        $css = '#successful-form';
        $attribute = 'method';
        $value = 'SOMERANDOMVALUE';

        $this->assertNotContains($value, $this->context->theElementAttributeShouldNotContainValue($css, $attribute, $value));
    }

    public function testFindOneOrFailWillFindOneElement() {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);

        $selector = 'css';
        $locator = 'li.nested';

        $element = $this->context->findOneOrFail($selector, $locator);

        $this->assertEquals('Nested', $element->getHtml());
    }

    public function testFindOneOrFailWillFailToFindOneElement() {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);

        $selector = 'css';
        $locator = 'li.non-existant-class';

        $this->expectException(ExpectationException::class);

        $this->context->findOneOrFail($selector, $locator);
    }

    /**
     * Test that #iframe-text will return different text depending if DOM reference is pointing to iframe or not
     */

    public function testSwitchToSingleIframeWillSwitchDomReferenceToThatIframe() {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);

        /* document.querySelector("'. $css . '") */
        $css = '#iframe-text';

        $text = $this->minkSession->evaluateScript("return document.querySelector('". $css . "').text");

        $iframeName = 'test-iframe';

        $this->context->iSwitchToSingleIframe($iframeName);

        $iframeText = $this->minkSession->evaluateScript("return document.querySelector('". $css . "').text");

        $this->assertEquals('IFRAME TEXT', $iframeText);
    }

    /**
     * Test that #iframe-text DOM reference will not longer point to that specific iframe, after switching out
     */

    public function testSwitchOutOfIFrameWillSwitchDomReferenceNotToPointToThatIframe() {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);

        /* document.querySelector("'. $css . '") */
        $css = '#iframe-text';

        $text = $this->minkSession->evaluateScript("return document.querySelector('". $css . "').text");

        $iframeName = 'test-iframe';

        $this->context->iSwitchToSingleIframe($iframeName);
        $this->context->iSwitchOutOfIFrame();

        $iframeText = $this->minkSession->evaluateScript("return document.querySelector('". $css . "').text");

        $this->assertEquals('NOT IFRAME TEXT', $iframeText);
    }

    public function testWaitForMilliseconds() {
//        $url = $this->server->getUrl('/');
//
//        $this->minkSession->visit($url);
//
//        $startTime = microtime(true);
//        $this->context->waitformilliseconds(3000);
//        $endTime = microtime(true);
//
//        $timeElapsed = $endTime - $startTime;
    }

    public function testFindAllOrFailWillNotFindAnyElements() {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);

        $selector = 'css';
        $locator = 'section'; # <section></section> HTML tag

        $this->expectException(ExpectationException::class);

        $this->context->findAllOrFail($selector, $locator);

    }

    public function testFindAllOrFailWillFindAllElementsSpecifiedByLocator() {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);

        $selector = 'css';
        $locator = 'ul'; # <section></section> HTML tag


        $elements = $this->context->findAllOrFail($selector, $locator);

        $this->assertCount(2, $elements);

    }

    public function testFindOrFailFromNodeWillFindAnElement() {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);

        $elementXpath = '/html/body/ul[2]';
        $element = new NodeElement($elementXpath, $this->minkSession);
        $selector = 'css';
        $locator = 'li.nested';

        $node = $this->context->findOrFailFromNode($element, $selector, $locator);

        $this->assertEquals('Nested', $node->getHtml());

    }

    public function testFindOrFailFromNodeWillFailToFindAnElement() {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);

        $elementXpath = '/html/body/ul[2]';
        $element = new NodeElement($elementXpath, $this->minkSession);
        $selector = 'css';
        $locator = 'li.non-existent-nested';

        $this->expectException(ExpectationException::class);

        $this->context->findOrFailFromNode($element, $selector, $locator);
    }

    public function testFindAllOrFailFromNodeWillFindElements() {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);

        $elementXpath = '/html/body/ul[2]';
        $element = new NodeElement($elementXpath, $this->minkSession);
        $selector = 'css';
        $locator = 'li';

        $elements = $this->context->findAllOrFailFromNode($element, $selector, $locator);

        $this->assertCount(4, $elements);
    }

    public function testFindAllOrFailFromNodeWillFailToFindElements() {
        $url = $this->server->getUrl('/');

        $this->minkSession->visit($url);

        $elementXpath = '/html/body/ul[2]';
        $element = new NodeElement($elementXpath, $this->minkSession);
        $selector = 'css';
        $locator = 'li.non-existent-nested';

        $this->expectException(ExpectationException::class);

        $this->context->findAllOrFailFromNode($element, $selector, $locator);
    }

    public function testGetTableWillExtractTableValuesFromNodeElementToArray() {
        $url = $this->server->getUrl('/table');

        $this->minkSession->visit($url);

        $elementXpath = '';
        $element = new NodeElement($elementXpath, $this->minkSession);

        $table = $this->context->getTable($element);

        $this->assertCount(3, $table);
    }


}