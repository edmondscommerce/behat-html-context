<?php declare(strict_types=1);

namespace EdmondsCommerce\BehatHtmlContext;


use Behat\Mink\Mink;

class HtmlContextTest extends AbstractTestCase
{
    /**
     * @var HtmlContext
     */
    private $context;

    public function setUp()
    {
        parent::setUp();

        //Set up the mock server
        //Set up Mink in the class
        $context = new HTMLContext();
        $context->setMink(new Mink([$this->minkSession]));
    }

    public function testClickOnTextWillFindTheTextAndClick()
    {
        $this->minkSession->visit('https://www.edmondscommerce.co.uk');
        echo $this->minkSession->getPage()->getContent();
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