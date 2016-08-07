# PHP-Drafter
This is a parser for API Blueprint files in PHP.

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

## Usage
For direct usage you can run:
```bash
$ ./php-drafter.phar blueprint-file.apib > blueprint-webpage.html
```
You can also install it first:
```bash
$ cp php-drafter.phar /usr/bin/php-drafter
$ chmod +x /usr/bin/php-drafter
$ php-drafter blueprint-file.apib > blueprint-webpage.html
```

## Dependencies

PHP-Drafter requires [drafter](https://github.com/apiaryio/drafter) to be installed. Refer to the drafter page for the installation details.

### Libraries
This app usage the following libraries:
* https://github.com/michelf/php-markdown.git