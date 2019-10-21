<?php namespace EdmondsCommerce\BehatHtmlContext;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ExpectationException;
use Behat\MinkExtension\Context\RawMinkContext;
use Exception;
use RuntimeException;

/**
 * Class HTMLContext
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @package EdmondsCommerce\BehatHtmlContext
 */

class HTMLContext extends RawMinkContext
{

    /**
     * Click some text
     *
     * @When /^I click on the text "([^"]*)"$/
     * @param string $text
     * @throws \UnexpectedValueException
     */
    public function iClickOnTheText($text)
    {
        $session = $this->getSession();
        $element = $session->getPage()->find(
            'xpath',
            '/*//*[text()[contains(.,"' . $text . '")]]'
        );
        if (null === $element) {
            throw new \UnexpectedValueException(sprintf('Cannot find text: "%s"', $text));
        }

        $element->click();
    }

    /**
     * Click first instance of visible text
     *
     * @When /^I click on the first visible text "([^"]*)"$/
     * @param string $text
     * @throws \UnexpectedValueException
     */
    public function iClickOnTheFirstVisibleText($text)
    {
        $session = $this->getSession();

        /** @var NodeElement[] $elements */
        $elements = $session->getPage()->findAll(
            'xpath',
            '/*//*[text()[contains(.,"' . $text . '")]]'
        );

        if (count($elements) === 0) {
            throw new \UnexpectedValueException(sprintf('Cannot find text: "%s"', $text));
        }

        foreach ($elements as $element) {
            if ($element->isVisible()) {
                $element->click();
                return;
            }
        }

        throw new \UnexpectedValueException(sprintf('Cannot find text that is visible: "%s"', $text));
    }

    /**
     * Click on element with CSS
     *
     * @When /^I click on the element "([^"]*)"$/
     * @param string $css
     * @throws \UnexpectedValueException
     */
    public function iClickOnTheElement($css)
    {
        $session = $this->getSession();
        $element = $session->getPage()->find('css', $css);
        if (null === $element) {
            throw new \UnexpectedValueException(sprintf('Cannot find element with css: "%s"', $css));
        }

        $element->click();
    }

    /**
     * Scroll to element with CSS
     * @When /^I scroll to "([^"]*)"$/
     * @param string $css
     */
    public function iScrollTo($css)
    {
        $session = $this->getSession();
        $session->executeScript('
            var $elem = document.querySelector("' . $css . '");
            var position = {
                left: $elem.offsetLeft,
                top: $elem.offsetTop
            };
           
            window.scrollTo(position.left, position.top);
        ');
    }

    /**
     * Scroll to element with CSS
     *
     * @When /^I hide element "([^"]*)"$/
     * @param string $css
     */
    public function iHideElement($css)
    {
        $session = $this->getSession();
        $session->executeScript('
            document.querySelector("'. $css . '").style.display = "none";
        ');
        $session->wait(5000);
    }

    /**
     * @param string $css
     * @Then /^I should match the element "([^"]*)"$/
     * @return bool
     * @throws \UnexpectedValueException
     */
    public function iMatchTheElement($css): bool
    {
        $session = $this->getSession();
        $element = $session->getPage()->find('css', $css);
        if (null === $element) {
            throw new \UnexpectedValueException(sprintf('Cannot find element with css: "%s"', $css));
        }

        return true;
    }

    /**
     * @param string $css
     * @Then /^I should not match the element "([^"]*)"$/
     * @return bool
     * @throws \UnexpectedValueException
     */
    public function iDontMatchTheElement($css): bool
    {
        $session = $this->getSession();
	$element = $session->getPage()->find('css', $css)->isVisible();
        if (null !== $element)
        {
            throw new \UnexpectedValueException(sprintf('Found element with css: "%s"', $css));
        }

        return true;
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
     * @param string $arg1
     */
    public function iSwitchToSingleIframe($arg1)
    {
        $this->getSession()->switchToIFrame($arg1);
    }

    /**
     * More about selector types can be read here http://mink.behat.org/en/latest/guides/traversing-pages.html#selectors
     *
     * @Given /^I switch to iframe identified by the following selector type "([^"]*)" and it's value "([^"]*)"$/
     * @param string $selectorType
     * @param string $locator
     * @throws ExpectationException
     */

    public function iSwitchToSingleIframeBySelector($selectorType, $locator)
    {
        $node = $this->findOneOrFail($selectorType, $locator);
        $session = $this->getSession();

        $nameAttributeValue = $node->getAttribute('name');

        if (true !== $node->hasAttribute('name')) {
            $nameAttributeValue = 'js-test-iframe';

            switch ($selectorType) {
                case 'css':
                    $session->executeScript("
                        document.querySelector('" . $locator . "')
                            .setAttribute('name', '" . $nameAttributeValue . "');");
                    break;
                case 'xpath':
                    $session->executeScript("
                        document.evaluate('" . $locator . "', document, null, XPathResult.FIRST_ORDERED_NODE_TYPE, null)
                            .singleNodeValue.setAttribute('name', '" . $nameAttributeValue . "');");
                    break;
            }
        }

        $session->switchToIFrame($nameAttributeValue);
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
     * @param int $mseconds
     */
    public function waitformilliseconds($mseconds)
    {
        $this->getSession()->wait($mseconds);
    }

    /**
     * @Then I scroll to the element with :selectortype :css
     * @param string $selectortype
     * @param string $selector
     * @throws \RuntimeException
     */
    public function iScrollToElement($selectortype, $selector)
    {
        if (!\in_array($selectortype, array('class', 'id'), true)) {
            throw new RuntimeException("Selector type has to be either 'class' or 'id'");
        }
        if (\in_array($selector[0], array('.', '#'), true)) {
            throw new RuntimeException('Selector should plain without a . or #');
        }

        if ($selectortype === 'class') {
            $script = <<<JS
document.getElementsByClassName('$selector').item(0).scrollIntoView();
JS;

            $this->getSession()->executeScript($script);
        }
        if ($selectortype === 'id') {
            $this->getSession()->executeScript("document.getElementById('" . $selector . "').scrollIntoView();");
        }
    }

    /**
     * @Then I should see one of the elements :css1 or :css2
     * @param string $css1
     * @param string $css2
     * @return bool
     * @throws \RuntimeException
     */
    public function iShouldSeeXorY($css1, $css2): bool
    {

        $elements1 = $this->getSession()
            ->getPage()
            ->findAll('css', $css1);
        $elements2 = $this->getSession()
            ->getPage()
            ->findAll('css', $css2);

        if (sizeof($elements1) > 0 xor sizeof($elements2) > 0) {
            return true;
        }

        throw new RuntimeException('Either both or none of the selected elements were found');
    }

    /**
     * @Then I submit the form :id
     * @param string $identifier
     */
    public function iSubmitTheForm($identifier)
    {
        $this->getSession()->evaluateScript("document.getElementById('" . $identifier . "').submit();");
    }


    /**
     * @Then the select :name should contain an option :value
     * @param string $name
     * @param string $value
     * @return bool
     * @throws \RuntimeException
     */
    public function theSelectShouldContainValue($name, $value): bool
    {

        $select = $this->getSession()
            ->getPage()
            ->find('named', array('select', $name));
        if (null !== $select && $select->has('named', array('option', $value))) {
            return true;
        }

        throw new RuntimeException('Element ' . $name . ' should contain an option named '
            . $value . ' but one was not found');
    }

    /**
     * @Then the select :name should not contain an option :value
     * @param string $name
     * @param string $value
     * @return bool
     * @throws \RuntimeException
     */
    public function theSelectShouldNotContainValue($name, $value): bool
    {

        $select = $this->getSession()
            ->getPage()
            ->find('named', array('select', $name));
        if (null !== $select && !$select->has('named', array('option', $value))) {
            return true;
        }

        throw new RuntimeException('Element ' . $name . ' should not contain an option named '
            . $value . ' but one was found');
    }

    /**
     * @Then the element :css attribute :attribute should not contain :value
     * @param string $css
     * @param string $attribute
     * @param string $value
     * @return null|string
     * @throws \RuntimeException
     */
    public function theElementAttributeShouldNotContainValue($css, $attribute, $value)
    {
        $element = $this->getSession()
            ->getPage()
            ->find('css', $css);

        if (null === $element) {
            throw new RuntimeException('No element matching the CSS ' . $css . ' was found');
        }

        $attributeValue = $element->getAttribute($attribute);

        if (strpos($attributeValue, $value) !== false) {
            throw new RuntimeException('The element ' . $css . "'s attribute " . $attribute . ' contains ' . $value);
        }

        return $attributeValue;
    }

    /**
     * @param string $selector
     * @param string $locator
     * @param null $message
     * @return NodeElement
     * @throws ExpectationException
     */
    public function findOneOrFail($selector, $locator, $message = null): NodeElement
    {
        $search = $this->getSession()->getPage()->find($selector, $locator);
        if ($search === null) {
            $locator = (is_array($locator) ? implode(':', $locator):  $locator);
            $message = $message ?? 'Could not find the element ' . $locator;
            throw new ExpectationException($message, $this->getSession()->getDriver());
        }

        return $search;
    }

    /**
     * Trys to find one or many items, will fail if the result count is 0 or result is null
     * @param string $selector
     * @param string $locator
     * @param null $message
     * @return NodeElement[]
     * @throws ExpectationException
     */
    public function findAllOrFail($selector, $locator, $message = null): array
    {
        $search = $this->getSession()->getPage()->findAll($selector, $locator);
        if (count($search) === 0) {
            $locator = (is_array($locator) ? implode(':', $locator):  $locator);
            $message = $message ?? 'Could not find any elements ' . $locator;
            throw new ExpectationException($message, $this->getSession()->getDriver());
        }

        return $search;
    }/** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param NodeElement $element
     * @param string $selector
     * @param string $locator
     * @param null $message
     * @return NodeElement|mixed|null
     * @throws ExpectationException
     */
    public function findOrFailFromNode(NodeElement $element, $selector, $locator, $message = null)
    {
        $result = $element->find($selector, $locator);
        if ($result === null) {
            $message = $message ?? 'Could not find the element ' . $locator;
            throw new ExpectationException($message, $this->getSession()->getDriver());
        }

        return $result;
    }/** @noinspection MoreThanThreeArgumentsInspection */

    /**
     * @param NodeElement $element
     * @param string $selector
     * @param string $locator
     * @param null $message
     * @return NodeElement[]
     * @throws ExpectationException
     */
    public function findAllOrFailFromNode(NodeElement $element, $selector, $locator, $message = null): array
    {
        $result = $element->findAll($selector, $locator);
        if (count($result) === 0) {
            $message = $message ?? 'Could not find the element ' . $locator;
            throw new ExpectationException($message, $this->getSession()->getDriver());
        }

        return $result;
    }

    /**
     * Extracts a table's values in to a standard PHP array from a node
     * Can also be passed tbody, thead, tfoot as well as table
     * @param NodeElement $element
     * @return array
     */
    public function getTable(NodeElement $element): array
    {
        //Extract the table data
        $result = [];

        /** @var NodeElement $row */
        foreach ($element->findAll('css', 'tr') as $row) {
            $rowResult = [];
            /** @var NodeElement $cell */
            foreach ($row->findAll('css', 'th,td') as $cell) {
                $rowResult[] = $cell->getText();
            }
            $result[] = $rowResult;
        }

        return $result;
    }

    /**
     * @Given /^I click on the first visible button "([^"]*)"$/
     * @param string $buttonText
     *
     * @throws ExpectationException
     */
    public function iClickOnTheFirstVisibleButton(string $buttonText): void
    {
        $xpath = sprintf('//button[contains(., "%s")]', $buttonText);
        $elements = $this->findAllOrFail('xpath',$xpath);
        foreach ($elements as $element) {
            if($element->isVisible()){
                $element->click();
                return;
            }
        }

        $message = 'Could not find visible button with text '.$buttonText;
        throw new ExpectationException($message, $this->getSession()->getDriver());
    }
}
