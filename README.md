# Behat HTML Context
## By [Edmonds Commerce](https://www.edmondscommerce.co.uk)

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/e2a47b219c934d3c93e15f5aaa451857)](https://www.codacy.com/app/edmondscommerce/behat-html-context?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=edmondscommerce/behat-html-context&amp;utm_campaign=Badge_Grade)

A simple Behat Context for working with HTML and navigation

### Installation

Install via composer

    "edmondscommerce/behat-html-context": "0.0.1"

### Include Context in Behat Configuration
        
    default:
        # ...
        suites:
            default:
                # ...
                contexts:
                    - # ...
                    - EdmondsCommerce\BehatHtmlContext\HTMLContext
                    - EdmondsCommerce\BehatHtmlContext\RedirectionContext
                    

>    `EdmondsCommerce\BehatHtmlContext\RedirectionContext` is no longer supported and marked as deprecated.
                    
## Helper Methods
You will need to load the HTML context as part of your own suite of contexts using `@BeforeScenario` to access the helper methods

All helpers accept a custom message to use with the exception 
```php

// Instantiate context class, 
$args = [
    'args' => [
        '--disable-gpu',
        '--window-size=1920,1080',
        '--start-maximised',
    ],
];

$driver = new Selenium2Driver('chrome', $args);
$session = new Behat\Mink\Session($driver);
$mink = new Mink(['selenium2' => $session]);

$htmlContext = new HTMLContext();
$htmlContext->setMink($mink);

//Find a node and return it or throw an exception
$htmlContext->findOneOrFail($selector, $locator, $message = null)

//Find multiple nodes and return an array of them or throw an exception if none are found
$htmlContext->findAllOrFail($selector, $locator, $message = null)

//Same as findOrFail but searches from the context of another node, can be useful for chaining
$htmlContext->findOrFailFromNode(\Behat\Mink\Element\NodeElement $element, $selector, $locator, $message = null)
```

## Developer dependencies

### edmondscommerce/phpqa

Simple PHP QA pipeline and scripts, that helps achieving high quality of code. [Click here](https://github.com/edmondscommerce/phpqa) for more details.

### edmondscommerce/mock-server

PHP Built-in web server. [Click here](https://github.com/edmondscommerce/mock-server) for more details.

### behat/mink-goutte-driver

Goutte driver. [Click here](https://github.com/minkphp/MinkGoutteDriver) for more details.

### behat/mink-selenium2-driver

Selenium2 driver. [Click here](https://github.com/minkphp/MinkSelenium2Driver) for more details.
