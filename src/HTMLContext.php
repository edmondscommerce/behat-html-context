<?php namespace EdmondsCommerce\BehatHtmlContext;

use Behat\Mink\Element\NodeElement;
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
        if (null === $element)
        {
            throw new \InvalidArgumentException(sprintf('Cannot find text: "%s"', $text));
        }

        $element->click();
    }

    /**
     * Click first instance of visible text
     *
     * @When /^I click on the first visible text "([^"]*)"$/
     */
    public function iClickOnTheFirstVisibleText($text)
    {
        $session = $this->getSession();

        /** @var NodeElement[] $elements */
        $elements = $session->getPage()->findAll(
            'xpath',
            '/*//*[text()[contains(.,"' . $text . '")]]'
        );

        if (count($elements) == 0)
        {
            throw new \InvalidArgumentException(sprintf('Cannot find text: "%s"', $text));
        }

        foreach ($elements as $element)
        {
            if ($element->isVisible())
            {
                $element->click();
                return;
            }
        }

        throw new \InvalidArgumentException(sprintf('Cannot find text that is visible: "%s"', $text));
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
        if (null === $element)
        {
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
            var $elem = jQuery("' . $css . '");
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
            $("' . $css . '").hide();
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
        if (null === $element)
        {
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
        if (null !== $element)
        {
            throw new \InvalidArgumentException(sprintf('Found element with css: "%s"', $css));
        }
    }


    /**
     * @Then /^I maximize browser window size$/
     * @throws Exception
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
     * @throws Exception
     */
    public function iScrollToElement($selectortype, $selector)
    {
        if (!in_array($selectortype, array("class", "id")))
        {
            throw new Exception("Selector type has to be either 'class' or 'id'");
        }
        if (in_array(substr($selector, 0, 1), array(".", "#")))
        {
            throw new Exception("Selector should plain without a . or #");
        }

        if ($selectortype == "class")
        {
            $this->getSession()->evaluateScript("document.getElementsByClassName('" . $selector . "').item(0).scrollIntoView();");
        }
        if ($selectortype == "id")
        {
            $this->getSession()->evaluateScript("document.getElementById('" . $selector . "').scrollIntoView();");
        }
    }

    /**
     * @Then I should see one of the elements :css1 or :css2
     * @throws Exception
     */
    public function iShouldSeeXorY($css1, $css2)
    {

        $elements1 = $this->getSession()
            ->getPage()
            ->findAll("css", $css1);
        $elements2 = $this->getSession()
            ->getPage()
            ->findAll("css", $css2);

        if (sizeof($elements1) > 0 xor sizeof($elements2) > 0)
        {
            return true;
        }

        throw new Exception('Either both or none of the selected elements were found');
    }

    /**
     * @Then I submit the form :id
     */
    public function iSubmitTheForm($id)
    {
        $this->getSession()->evaluateScript("document.getElementById('" . $id . "').submit();");
    }


    /**
     * @Then the select :name should contain an option :value
     * @throws Exception
     */
    public function theSelectShouldContainValue($name, $value)
    {

        $select = $elements1 = $this->getSession()
            ->getPage()
            ->find("named", array('select', $name));
        if ($select->has('named', array('option', $value)))
        {
            return true;
        }

        throw new Exception('Element ' . $name . ' should contain an option named ' . $value . " but one was not found");

    }

    /**
     * @Then the select :name should not contain an option :value
     * @throws Exception
     */
    public function theSelectShouldNotContainValue($name, $value)
    {

        $select = $elements1 = $this->getSession()
            ->getPage()
            ->find("named", array('select', $name));
        if (!$select->has('named', array('option', $value)))
        {
            return true;
        }

        throw new Exception('Element ' . $name . ' should not contain an option named ' . $value . " but one was found");

    }

    /**
     * @Then the element :css attribute :attribute should contain :value
     * @throws Exception
     */
    public function theElementAttributeShoudldNotContainValue($css, $attribute, $value)
    {
        $element = $this->getSession()
            ->getPage()
            ->find("css", $css);

        if (is_null($element))
        {
            throw new Exception("No element matching the CSS " . $css . " was found");
        }

        $attributeValue = $element->getAttribute($attribute);

        if (strpos($attributeValue, $value) === false)
        {
            throw new Exception("The element " . $css . "'s attribute " . $attribute . " doesn't contain " . $value);
        }

    }

    /**
     * @Then the element :css attribute :attribute should not contain :value
     * @throws Exception
     */
    public function theElementAttributeShouldNotContainValue($css, $attribute, $value)
    {
        $element = $this->getSession()
            ->getPage()
            ->find("css", $css);

        if (is_null($element))
        {
            throw new Exception("No element matching the CSS " . $css . " was found");
        }

        $attributeValue = $element->getAttribute($attribute);

        if (strpos($attributeValue, $value) !== false)
        {
            throw new Exception("The element " . $css . "'s attribute " . $attribute . " doesn't contain " . $value);
        }
    }

    /**
     * @param $selector
     * @param $locator
     * @param null $message
     * @return \Behat\Mink\Element\NodeElement
     * @throws \Behat\Mink\Exception\ExpectationException
     */
    public function findOneOrFail($selector, $locator, $message = null)
    {
        $search = $this->getSession()->getPage()->find($selector, $locator);
        if ($search === null)
        {
            $message = ($message === null) ? 'Could not find the element ' . $locator : $message;
            throw new \Behat\Mink\Exception\ExpectationException($message, $this->getSession()->getDriver());
        }

        return $search;
    }

    /**
     * Trys to find one or many items, will fail if the result count is 0 or result is null
     * @param $selector
     * @param $locator
     * @param null $message
     * @return \Behat\Mink\Element\NodeElement[]
     * @throws \Behat\Mink\Exception\ExpectationException
     */
    public function findAllOrFail($selector, $locator, $message = null)
    {
        $search = $this->getSession()->getPage()->findAll($selector, $locator);
        if (count($search) === 0 || $search === null)
        {
            $message = ($message === null) ? 'Could not find any elements ' . $locator : $message;
            throw new \Behat\Mink\Exception\ExpectationException($message, $this->getSession()->getDriver());
        }

        return $search;
    }

    /**
     * @param \Behat\Mink\Element\NodeElement $element
     * @param $selector
     * @param $locator
     * @param null $message
     * @return \Behat\Mink\Element\NodeElement|mixed|null
     * @throws \Behat\Mink\Exception\ExpectationException
     */
    public function findOrFailFromNode(\Behat\Mink\Element\NodeElement $element, $selector, $locator, $message = null)
    {
        $result = $element->find($selector, $locator);
        if ($result === null)
        {
            $message = ($message === null) ? 'Could not find the element ' . $locator : $message;
            throw new \Behat\Mink\Exception\ExpectationException($message, $this->getSession()->getDriver());
        }

        return $result;
    }

}
