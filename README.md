Argvs
-----

Argvs is a cli argument parsing library for PHP.

#### Set up...

In your `composer.json` file:

```json
{
    "repositories":[
        {
            "type": "vcs",
            "url": "git@github.com:rogerthomas84/argvs.git"
        }
    ],
    "require": {
        "rogerthomas84/argvs": ">=0.0.1"
    }
}
```

#### Usage...

```php
<?php
$argvs = \Argvs\Argvs::getInstance($argv, $argc);

// run the script.
```

#### Working with flags...

```php
<?php
$argvs = \Argvs\Argvs::getInstance($argv, $argc);

if ($argvs->hasHelp()) {
    // help has been passed
}
if ($argvs->hasVerbose()) {
    // verbose has been passed
}
if ($argvs->hasFlag('foobar')) {
    // foobar has been passed
}

$age = $argvs->getArg('age');

```


#### Running Unit Tests...

`./vendor/bin/phpunit -c phpunit.xml`
