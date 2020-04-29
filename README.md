# Extensions for Bugsnag Symfony

This library provides middleware and listeners to fit our own specific uses of [Bugsnag](https://bugsnag.com/).  

## Requirements

* `PHP >= 7.1`
* `symfony >= 4.0`
* `bugsnag/bugsnag-symfony >= 1.6`

## Installation

```
composer require beapp/bugsnag-symfony-ext
```

Add the following configuration :

```
bugsnag_ext:
  handled_exceptions:
    - 'App\Exception\LogicException'
  excluded_http_codes: ["4xx", 301]
  session_per_request: true
```

## Features

* `handled_exceptions` : The exceptions matching one these will be forced to be notified as handled on Bugsnag platform
* `excluded_http_codes` : Ignore exceptions matching specific Http response status code. Use "x" as a placeholder for any number.
* `session_per_request` : Register a listener to start a session for every request 
