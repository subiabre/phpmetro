# PHPMetro
Statistical analysis for PHP code.

1. [About](#About)
2. [Installation](#Installation)
3. [Configuration](#Configuration)
4. [Usage (basic tutorial)](#Usage)

## About
I created this package on winter 2020 because I was working on a project that required me to not only perform unit and functional tests of my code, but to also create and perform several statistical analysis of my code. PHPMetro was my response to that requirement.

>**WARNING**: Despite the origin of this project being that of a professional environment, it's not granted to be suitable for all production environments.

## Installation
PHPMetro is distributed on [packagist](https://packagist.org/packages/subiabre/phpmetro). Get it using [composer](https://getcomposer.org).

```console
$ composer require --dev subiabre/phpmetro
```

Once installed you'll get the phpmetro binary in your vendor folder. After installation you'll want to run `composer suggests subiabre/phpmetro`.

## Configuration
PHPMetro **needs** to have a `phpunit.xml` file at the root of your project. This file will tell where to search for our Analysis classes and how to run them.

To easily create this file you can run the `phpmetro` binary and it will automatically detect the lack of config file and copy the template one for you:

```console
$ ./vendor/bin/phpmetro
```

Once copied you should update the **namespace** attribute and the suite(s) **directory** to match those of your project.

>**NOTE**: It's assummed you'll only need PHPMetro on development. Avoid possible vulnerabilities by requiring it only on dev.

## Usage
Say you have your own random number generator (for whatever reason), `MyApp\RandomNumber` and want to see some statistics about it. PHPMetro is your package for that! Let's put it under "Analysis".

An *Analysis* is an special class that extends from the `AnalysisCase` class and contains a set up with Samples and several Tests over the samples.

```php
<?php
# tests/RandomNumber/RandomNumberAnalysis.php
namespace MyApp\Tests\RandomNumber;

use MyApp\RandomNumber;
use PHPMetro\Analysis\AnalysisCase;

class RandomNumberAnalysis extends AnalysisCase
{
    # ...
}

```

Congratulations, you just created your very first Analysis. But just like that it's pretty much useless. Now you need to add some Samples to your Analysis.

A *Sample* is an special array inside the `AnalysisCase` class that contains sample data from a given set of functions. On your Analysis class add:

```php
public function setUp(): void
{
    $this->addSample('RandomNumber', 100, function(){
        $randomNumber = new RandomNumber();

        return $randomNumber->new();
    });
}
```

To add data to a sample we use the `addSample` function, this function takes exactly 3 parameters:

- **$name**: The sample name. String.
- **$size**: The sample size in number of records, i.e the number of times to perform the function. Integer.
- **$function**: The function to be run on each iteration of the sample. Needs to return a value or will be ignored from the sample. Callable.

>**NOTE**: Function calls that don't return a value will not be added to the Sample, resulting in an array of less size than the specified one.

You can call to `addSample` anywhere in your Analyses, actually, this function will hold the execution of the code until it finishes adding records. However it is recommended that you add your Samples on set up for performance reasons.

>**NOTE**: Unlike other testing frameworks and libraries, PHPMetro will run the class setUp only once before running all tests.

Now that there is Sample data we can start performing some calculations on it. For that you must simply just add Test methods in your class.

A *Test* is an special class method inside your Analysis class that performs some kind of calculation and returns the result. These methods must match the regular expression: `/test[A-Za-z09]*\(\)/` and return a basic data type.

>**WARNING**: Tests that return complex data types such as arrays or objects that can't be casted to strings will throw an error exception and stop the PHPMetro run.

For example, we want to test what's the median average of the generated random numbers:

```php
public function testMedianAverage()
{
    $sample = $this->sample['RandomNumbers'];

    $total = \count($sample);
    $sum = 0;

    foreach ($sample as $number)
    {
        $sum += $number;
    }

    return $sum / $total;
}
```

To calculate it we first accessed the `sample` internal variable, which is where our Samples are added, and then counted the sum of the total values and the number of values. Your classic average.

You can keep writing more Tests to calculate different things with your Samples.

Finally, to run the Analyses (and assuming you've configured your .xml file) run the binary:

```console
$ ./vendor/bin/phpmetro
```

Your Tests results should start appearing on your console screen nested by Analysis class.
