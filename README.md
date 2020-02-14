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

Create a new `phpmetro.xml` file in the root folder of your project. This file will contain definitions for PHPMetro like test suites and configuration options. For now since PHPMetro is in a very early alpha it just defines the tests folder.

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpmetro>
    <directory>tests/phpmetro</directory>
</phpmetro>
```

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

Congratulations, you'd just created your first PHPMetro Analysis. Extending from `Analysis` is the first step to create tests and perform analysis of our random number generator.

Now we need some results:

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

There we started our test and added a sample. ***Samples*** are just internal arrays that store several values. We generate them by calling `addSample`. This function takes a name, a sample size or length (that is the size of the array) and a function.

This function will be called as many times as specified in the second parameter (100).

>The function passed will need to **return a value** in order to create a sample record. If it does not return any value, that iteration will be ignored from the record and our sample will not be of the specifed size.

Samples are *ideally* added on `setUp`. This method will run first, however notice that this method will only run once and not before each test. Also notice, tests in PHPMetro aren't isolated nor need they to run isolated.

You can add samples on your tests, but it's recommended and expected that you add your samples here before performing any tests.

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

We've just performed our first analysis: calculated the average of our random generator. We did so by summing the values inside our **Test** sample and then dividing the length of the sample by the sum of it's values. Your classical average median.

Once our calculations are perfomed we `return` the desired value.

You can write as many tests as you want, methods names must follow the regex `test[_A-Za-z1-9]` to be run and need to return a value. Tests that don't return a value will be ignored.

>Notice that despite the `test` prefix, these methods don't really perform assertions nor return comprobations. What we actually do in our tests is **analysis of the data in the sample**.

#### 4. Run the analysis

After you've wrote all the tests you wanted you'd probably like to run them and see the results.

```console
$ ./vendor/bin/phpmetro
```

And you should see your results on the screen. (This behaviour will likely be changed sooner than later)
