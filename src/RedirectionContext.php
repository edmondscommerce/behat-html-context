<?php namespace EdmondsCommerce\BehatHtmlContext;

use /** @noinspection PhpDeprecationInspection */
    Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Mink\Driver\GoutteDriver;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\MinkExtension\Context\RawMinkContext;

/** @noinspection PhpDeprecationInspection */
class RedirectionContext extends RawMinkContext implements SnippetAcceptingContext
{

    /**
     * @deprecated
     * @throws UnsupportedDriverActionException
     */
    public function canIntercept()
    {
        $driver = $this->getSession()->getDriver();
        if (!$driver instanceof GoutteDriver) {
            throw new UnsupportedDriverActionException(
                'You need to tag the scenario with "@mink:goutte" Intercepting the redirections is not supported by %s',
                $driver
            );
        }
    }

    /**
     * @deprecated
     * @Given I don't follow redirects
     */
    public function redirectsAreIntercepted()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->getSession()->getDriver()->getClient()->followRedirects(false);
    }

    /**
     * @deprecated
     * @When /^I follow the redirection$/
     * @Then /^I should be redirected$/
     * @throws \Behat\Mink\Exception\UnsupportedDriverActionException
     */
    public function iFollowTheRedirection()
    {
        /** @noinspection PhpDeprecationInspection */
        $this->canIntercept();
        /** @noinspection PhpUndefinedMethodInspection */
        $client = $this->getSession()->getDriver()->getClient();
        /** @noinspection PhpUndefinedMethodInspection */
        $client->followRedirects(true);
        /** @noinspection PhpUndefinedMethodInspection */
        $client->followRedirect();
    }
}
