# PHPDraft
This is a parser for API Blueprint files in PHP.

## Usage
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

## Including Files
It is possible to include other files in your blueprint by using a special include directive with a path to the included file relative to the current file's directory. Included files can be written in API Blueprint, Markdown or HTML (or JSON for response examples). Included files can include other files, so be careful of circular references.

```markdown
<!-- include(filename.md) -->
```

For tools that do not support this include directive it will just render out as an HTML comment. API Blueprint may support its own mechanism of including files in the future, and this syntax was chosen to not interfere with the [external documents proposal](https://github.com/apiaryio/api-blueprint/issues/20) while allowing `PHPDraft` users to include documents today.

_Thanks to [aglio](https://github.com/danielgtaylor/aglio) for the idea._

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


## Dependencies
PHPDraft requires [drafter](https://github.com/apiaryio/drafter) to be installed. Refer to the drafter page for the installation details.

## Libraries
This app usage the following libraries:
* https://github.com/michelf/php-markdown.git