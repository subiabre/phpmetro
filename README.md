# PHPMetro
Statistical analysis and testing for PHP.

This package provides an easy, tested API to write code tests to measure statistical results.

## What does *statistical analysis* mean?
I created this package because in a project I had to work at, I was required to not only do unit tests, but I had to also provide tests to statistically measure my code results. It was important to check that the code generated results under certain deviation and with certain success rates. I call this tests "statistical analysis tests".

To avoid writing ugly, unmaintanable, untrustworthy tests, I created this simple package.

## Installation

```console
$ composer require subiabre/phpmetro
```

### Usage

PHPMetro simply provides a wrapper to create statistical analysis for code results.

Say you have your own random number generator and you want to know the average number and other measures:

#### 0. Specify a PHPMetro suite

Create a new `phpmetro.xml` file in the root folder of your project. This file will contain definitions for PHPMetro like test suites and configuration options.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpmetro
    bootstrap="vendor/autoload.php"
    namespace="MyApp\Tests\Analysis"
    verbose="true"
    >
    <analysis>
        <suite name="My First Analysis Suite">
            <directory>tests/phpmetro</directory>
        </suite>
    </analysis>
</phpmetro>
```
The `phpmetro.xml` file defines general options for the PHPMetro runner. These options are all required to be present.

**bootstrap** defines the class mapper for autoloading. Usually this will be your regular composer autoloader.
**namespace** is the namespace you use for your analysis classes.
**verbose** when set to `true` will make the runner display additional info about the analysis and performance of your PHPMetro suites.

Suites are just groups of analysis that you wish to run. Your suites are nested under the **`<analysis>`** tag and must have a **name** attribute and have the **`<directory>`** tag where you specify the path to this suite relative to the root folder of your project.

#### 1. Create a new Analysis

```php
<?php
# tests/phpmetro/RandomAnalysis.php
namespace MyApp\Tests\Analysis;

use PHPMetro\Analysis;
use MyApp\Random;

class RandomAnalysis extends Analysis
{
    ...
}
```

Congratulations, you've just created your first PHPMetro Analysis. Extending from `PHPMetro\Analysis` is the first step to create tests and perform analysis of our random number generator.

Now we need some sample results to analyize:

#### 2. Add samples

```php
class RandomAnalysis extends Analysis
{
    public function setUp(): void
    {
        $this->addSample('Test', 100, function(){
            $random = new Random();

            return $random->new();
        });
    }
}
```

There we started our test and added a 'Test' sample. *Samples* are just internal arrays that store several values. We generate them by calling `addSample`. This function takes a name, a sample size or length (that is the size of the array) and a function.

The passed function will be called as many times as specified in the second parameter, 100 in our example.

>The function passed will need to **return a value** in order to create a sample record. If it does not return any value, that iteration will be ignored from the record and our sample will not be of the specifed size.

Samples are ideally added on `setUp`. This method will run first, however notice that this method will only run once and not before each test. Also notice, tests in PHPMetro aren't isolated nor need they to run isolated.

You can add samples later on your tests, but it's recommended and expected that you add your samples here before performing any tests.

#### 3. Analysing and performing tests on our sample

```php
    ...
    public function testAverage()
    {
        $total = 0;

        foreach ($this->sample['Test'] as $key => $value)
        {
            $total += $value;
        }

        $average = $total / count($this->sample['Test']);

        return $average;
    }
```

We've just performed our first analysis: we calculated the average of our random generator. We did so by summing the values inside our 'Test' sample and then dividing the length of the sample by the sum of it's values. Your classical median average.

>Once our calculations are perfomed we must return the value we want to know. Tests that don't **return a value** will appear as empty when running our analysis.

You can write as many tests as you want, methods names must follow the regex `test[_A-Za-z1-9]` to be run and need to return a value. Tests that don't return a value will be ignored.

>Notice that despite the `test` prefix, these methods don't really perform assertions nor return comprobations. What we actually do in our tests is an *analysis* of the data in the sample.

#### 4. Run the analysis results

After you've wrote all the tests you wanted you'd probably like to run them and see the results. On your console run:

```console
$ ./vendor/bin/phpmetro
```

And you should see your results on the screen. (This behaviour will likely be changed sooner than later)

The binary to trigger the runner takes a custom .xml config file location, if your configuration is elsewhere simply run:

```console
$ ./vendor/bin/phpmetro -c 'tests/phpmetro/myconfig.xml'
```
