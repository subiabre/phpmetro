# PHPMetro
Sampling and analysis for PHP code.

[![time tracker](https://wakatime.com/badge/gitlab/subiabre/phpmetro.svg)](https://wakatime.com/badge/gitlab/subiabre/phpmetro)

1. [About](#About)
2. [Installation](#Installation)
3. [Configuration](#Configuration)
4. [Usage](#Usage)

## About
PHPMetro is a library created to simplify the process of creating statistical analysis of PHP code.

It does so using *Analysis* cases, *Samples* and *Tests*.

In this library terms:
1. an **Analysis** is a class that contains internal samples with a number of records and several tests that work on the samples.
2. a **Sample** is an Analysis class internal array containing a collection of results from a given function. 
3. a **Test** is an Analysis class method that performs calculations and analysis on the class Sample.

PHPMetro allows developers to analize their code and perform tests on samples of their code results.

>I created this package as an internal tool for a project I worked at in winter 2020. I was required to not only perform unit tests of my code, but to also create tests to check that my code generated results within certain statistics.
Despite it's origin, it is not a perfect tool and it's suitability for production environments is not granted.
**Contributors are highly welcome**.

## Installation
PHPMetro is distributed using [composer](https://getcomposer.org).

```console
$ composer require subiabre/phpmetro
```

This way you'll get the `phpmetro` binary installed inside your vendor folder. This binary contains the code necessary to run all your tests from a single console command. You'll also get an example `phpmetro.xml` configuration file.

## Configuration
To use the binary it's **required** to have a `phpmetro.xml` config file at the root of your project. This file will contain PHPMetro configuration and the project's analysis suites.

Check the example phpmetro.xml file in this repository to see and understand how it's structured.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<!--PHPMetro example .xml configuration file-->
<phpmetro
    bootstrap="vendor/autoload.php"
    namespace="MyApp\Tests"
    verbose="true"
    >

    <analysis>
        <suite name="PHPMetro">
            <directory>tests</directory>
        </suite>
    </analysis>

</phpmetro>
```

The attributes you see under the `phpmetro` tags are all required and they mean:

1. **bootstrap** is the class mapper. Usually the composer autoloader.
2. **namespace** is the analysis classes common namespace.
3. **verbose** when set to `true` will tell the runner to display additional information about the running status.

## Usage
Using PHPMetro will be fairly familiar for developers experienced on PHPUnit.

To show how to use PHPMetro we will create an example analysis of an imaginary custom random generator: `MyApp\RandomNumber`.

#### 1. Create an analysis by extending from `PHPMetro\Analysis`:
```php
<?php
# tests/Random/RandomNumberAnalysis.php
namespace MyApp\Tests\Random;

use PHPMetro\Analysis;
use MyApp\RandomNumber;

class RandomNumberAnalysis extends Analysis
{
    ...
}

```
That's all it takes to start an analysis. PHPMetro will look through your analysis suite directory and automatically load all the analysis classes inside when you run it. Files **must end with `...Analysis.php`** to be recognized by PHPMetro.

To create more analysis cases simply create a new class extending from `PHPMetro\Analysis`.

#### 2. Add samples
When instantiated by the runner, this will call the method `setUp` of your class. This method will run only once at the beggining of your analysis and before all the tests. Here is where you should be adding samples.

```php
public function setUp(): void
{
    $this->addSample('Results', 100, function(){
        $random = new RandomNumber();

        return $random->new();
    });
}
```
To add samples to our analysis we call the `addSample` internal method. This method expects exactly 3 parameters:

1. `$name`.
2. `$size`.
3. `$function`.

When run, it will add a new array with the sample name as key to the internal samples array. This array will hold as many items as specified by the size, each containing the result of the passed in function.

>It is recommended to **always return a value** in the passed function. Iterations of the function that don't return a value will be ignored, resulting in a sample of a different length than the specified.

#### 3. Perform tests
Now that there is a sample, it is now time to analize it. After running your analysis set-up, the runner will then call all the methods that match the regex `test[A-Za-z0-9]*`, this is any method that starts with `test`.

```php
public function testAverageResult()
{
    $total = 0;

    foreach ($this->sample['Results'] as $key => $number) {
        $total += $number;
    }

    $average = $total / count($this->sample['Results']);

    return $average;
}
```

When running, PHPMetro will create an array of your analysis tests and run them one by one, printing to your console screen their return value. If you don't return a value, your test will show up as empty.

For our test we just calculated the median average of the results by diving the sum of them between the total of them.

>PHPMetro does not include any internal tools to perform tests calculations, you can use your preferred libraries and code to do your analysis. PHPMetro only helps to create your analysis in a standarized way and run them all from console without it being a hustle.
Check the suggested packages by PHPMetro to simplify mathematical operations and drawing charts.

#### 4. Run your suite
To run the analysis, trigger the runner from the root of your project:

```console
$ ./vendor/bin/phpmetro
```
If you did everything right, you should see your results on your console screen popping one by one.
