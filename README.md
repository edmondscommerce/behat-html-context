# Behat HTML Context
## By [Edmonds Commerce](https://www.edmondscommerce.co.uk)

A simple Behat Context for working with HTML and navigation

### Installation

Install via composer

    "edmondscommerce/behat-html-context": "~1.0"

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
                    
## Helper Methods
You will need to load the HTML context as part of your own suite of contexts using `@BeforeScenario` to access the helper methods

All helpers accept a custom message to use with the exception 
```php

//Find a node and return it or throw an exception
HTMLContext::findOneOrFail($selector, $locator, $message = null)

//Find multiple nodes and return an array of them or throw an exception if none are found
HTMLContext::findAllOrFail($selector, $locator, $message = null)

//Same as findOrFail but searches from the context of another node, can be useful for chaining
HTMLContext::findOrFailFromNode(\Behat\Mink\Element\NodeElement $element, $selector, $locator, $message = null)
```