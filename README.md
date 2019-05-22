# PHPDraft [![Packagist Version](https://img.shields.io/packagist/v/smillerdev/phpdraft.svg)](https://github.com/SMillerDev/phpdraft/releases/latest) [![Build Status](https://img.shields.io/travis/smillerdev/phpdraft.svg)](https://travis-ci.org/SMillerDev/phpdraft) [![codecov](https://img.shields.io/codecov/c/github/smillerdev/phpdraft.svg)](https://codecov.io/gh/SMillerDev/phpdraft) [![Style Status](https://styleci.io/repos/65147997/shield?style=square)](https://styleci.io/repos/65147997) ![Maintenance](https://img.shields.io/maintenance/yes/2019.svg) 
This is a parser for API Blueprint files in PHP.[1](#dependencies)

## Dependencies
PHPDraft requires [drafter](https://github.com/apiaryio/drafter) to be installed. Refer to the drafter page for the installation details. If you don't want to install drafter, you can pass `-o` to the command to make it use [https://api.apiblueprint.org/parser](https://api.apiblueprint.org/parser)

## Usage
Requires PHP 7.0+ to run. Unittests require runkit or uopz
For direct usage you can run:
```bash
$ ./phpdraft.phar -f blueprint-file.apib > blueprint-webpage.html
```
You can also install it first:
```bash
$ cp phpdraft.phar /usr/bin/phpdraft
$ chmod +x /usr/bin/phpdraft
$ phpdraft -f blueprint-file.apib > blueprint-webpage.html
```

## Extra features
We got some fun stuff, check the [wiki](https://github.com/SMillerDev/phpdraft/wiki) for more.

## Writing API documentation

For writing API documentation using [API Blueprint](http://apiblueprint.org/) syntax. You can read about its [specification](https://github.com/apiaryio/api-blueprint/blob/master/API%20Blueprint%20Specification.md).

Here's the example:

```markdown
FORMAT: 1A
HOST: https://api.example.com/v1

# Hello API

A simple API demo

# Group People

This section describes about the People

## Person [/people/{id}]

Represent particular Person

+ Parameters

    + id (required, string, `123`) ... The id of the Person.

+ Model (application/json)

    ```
    {"name":"Gesang","birthdate":"01-09-1917"}
    ```

### Retrieve Person [GET]

Return the information for the Person

+ Request (application/json)

    + Headers

        ```
        Authorization: Basic AbcdeFg=
        ```

+ Response 200 (application/json)

    [Person][]

```

## Building an executable
Install the binary dependencies with composer (`composer install`).
Run `ant phar` or `ant phar-nightly`

## Libraries
This app usage the following libraries:
* https://github.com/michelf/php-markdown.git
