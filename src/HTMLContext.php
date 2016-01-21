<?php namespace EdmondsCommerce\BehatHtmlContext;

use Behat\MinkExtension\Context\RawMinkContext;
use Exception;

class HTMLContext extends RawMinkContext
{

    /**
     * Click some text
     *
     * @When /^I click on the text "([^"]*)"$/
     */
    public function iClickOnTheText($text)
    {
        $session = $this->getSession();
        $element = $session->getPage()->find(
            'xpath',
            '/*//*[text()[contains(.,"' . $text . '")]]'
        );
        if (null === $element) {
            throw new \InvalidArgumentException(sprintf('Cannot find text: "%s"', $text));
        }

        $element->click();

    }

    /**
     * Click on element with CSS
     *
     * @When /^I click on the element "([^"]*)"$/
     */
    public function iClickOnTheElement($css)
    {
        $session = $this->getSession();
        $element = $session->getPage()->find('css', $css);
        if (null === $element) {
            throw new \InvalidArgumentException(sprintf('Cannot find element with css: "%s"', $css));
        }

        $element->click();
    }

    /**
     * Scroll to element with CSS
     *
     * @When /^I scroll to "([^"]*)"$/
     */
    public function iScrollTo($css)
    {
        $session = $this->getSession();
        $session->executeScript('
            var $elem = $("' . $css . '");
            var position = $elem.position();
            window.scrollTo(position.left, position.top);
        ');
    }

    /**
     * Scroll to element with CSS
     *
     * @When /^I hide element "([^"]*)"$/
     */
    public function iHideElement($css)
    {
        $session = $this->getSession();
        $session->executeScript('
            $("'.$css.'").hide();
        ');
        $session->wait(5000);
    }

    /**
     * @param $css
     * @Then /^I should match the element "([^"]*)"$/
     */
    public function iMatchTheElement($css)
    {
        $session = $this->getSession();
        $element = $session->getPage()->find('css', $css);
        if (null === $element) {
            throw new \InvalidArgumentException(sprintf('Cannot find element with css: "%s"', $css));
        }
    }

    /**
     * @param $css
     * @Then /^I should not match the element "([^"]*)"$/
     */
    public function iDontMatchTheElement($css)
    {
        $session = $this->getSession();
        $element = $session->getPage()->find('css', $css);
        if (null !== $element) {
            throw new \InvalidArgumentException(sprintf('Found element with css: "%s"', $css));
        }
    }


    /**
     * @Then /^I maximize browser window size$/
     */
    public function maximiseWindow()
    {
        $this->getSession()->getDriver()->maximizeWindow();
    }

    /**
     * @Given /^I switch to iframe "([^"]*)"$/
     */
    public function iSwitchToSingleIframe($arg1)
    {
        $this->getSession()->switchToIFrame($arg1);
    }

    /**
     * @Given /^I switch out of iframe$/
     */
    public function iSwitchOutOfIFrame()
    {
        $this->getSession()->switchToIFrame();
    }

    /**
     * @When I wait :arg1 milliseconds
     */
    public function waitformilliseconds($mseconds)
    {
        $this->getSession()->wait($mseconds);
    }

    /**
     * @Then I scroll to the element with :selectortype :css
     */
    public function iScrollToElement($selectortype, $selector)
    {
        if(!in_array($selectortype, array("class", "id"))) {
            throw new Exception("Selector type has to be either 'class' or 'id'");
        }
        if(in_array(substr($selector, 0, 1), array(".", "#"))) {
            throw new Exception("Selector should plain without a . or #");
        }

        if($selectortype == "class") {
            $this->getSession()->evaluateScript("document.getElementsByClassName('" . $selector . "').item(0).scrollIntoView();");
        }
        if($selectortype == "id") {
            $this->getSession()->evaluateScript("document.getElementById('".$selector."').scrollIntoView();");
        }
    }

    /**
     * @Then I should see one of the elements :css1 or :css2
     */
    public function iShouldSeeXorY($css1, $css2) {

        $elements1 = $this->getSession()
            ->getPage()
            ->findAll( "css", $css1 );
        $elements2 = $this->getSession()
            ->getPage()
            ->findAll( "css", $css2 );

        if(sizeof($elements1) > 0 xor sizeof($elements2) > 0) {
            return true;
        }

        throw new Exception('Either both or none of the selected elements were found');
    }

    /**
     * @Then I submit the form :id
     */
    public function iSubmitTheForm($id) {
        $this->getSession()->evaluateScript("document.getElementById('".$id."').submit();");
    }

}