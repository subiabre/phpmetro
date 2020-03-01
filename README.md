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

>**NOTE**: It's assummed you'll only need PHPMetro on development. Avoid possible vulnerabilities by requiring it only on dev.

## Configuration
PHPMetro **needs** to have a `phpmetro.xml` file at the root of your project. This file will tell where to search for our Analysis classes and how to run them.

### Getting the template configuration
To easily create this file you can run the `phpmetro` binary and it will automatically detect the lack of config file and copy the template one for you:

```console
$ ./vendor/bin/phpmetro
```

### Configuring
The `phpmetro.xml` file specifies general run directives for the PHPMetro binary as well as defines the analyses by suite groups.

This file can be environment specific, `phpmetro.xml.local` will override `phpmetro.xml` and `phpmetro.xml.dist`.

1. [Configuring PHPMetro](#Configuring-PHPMetro)
2. [Configuring Suites](#Configuring-Suites)

#### Configuring PHPMetro
```xml
<?xml version="1.0" encoding="UTF-8"?>

<phpmetro
    bootstrap="vendor/autoload.php"
    verbose="true" 
>

    ...

</phpmetro>

```

The two attributes you see are required and they mean:
- **bootstrap**: The classes mapper. Usually your composer autoload.
- **verbose**: When set to "true" PHPMetro will display additional run info.

There are no more run configurations. The runner will automatically fetch files from the suites you define. You can define more configurations per suite.

#### Configuring Suites
```xml
<suites>
    <suite
        name="My Analysis Suite"
        suffix="PHPMetroFile.php"
        ignore="false"
    >
        <namespace>MyApp\Tests</namespace>
        <directory>tests</directory>
    </suite>
</suites>

```

Suites must be inside the `<suites>` tag. Each suite accepts the following attributes:
- **name**: The display name of the suite. Required.
- **suffix**: Any combinations of letters that the analysis class files use as suffix. Optional. Defaults to "Analysis.php".
- **ignore**: When set to "true" it will indicate the runner to skip the execution of that suite. Optional. Defaults to "false".

>**NOTE**: Suffixes don't necessarily have to include the '.php' file extension. In fact the runner right-trims it from the suffix.

Inside each suite there must be two more tags specificating:
- **namespace**: The common namespace for the suite classes.
- **directory**: The folder where all the classes are at.

You must adhere to the [PSR-4](https://getcomposer.org/doc/04-schema.md#psr-4) specification when filenaming your Analyses.

If you have an Analysis with the namespace `MyApp\Tests\Foo\BarAnalysis`, for this class to be properly identified by PHPMetro it will have to be located at `tests/Foo/BarAnalysis.php`.

## Usage
Say you have your own random number generator: `MyApp\RandomNumber`, and want to see some statistics about it. PHPMetro is your package for that! Let's put it under "Analysis".

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

>**NOTE**: Function calls that don't return a value will not be added to the Sample, resulting in a size lesser than the specified one.

You can call to `addSample` anywhere in your Analyses, actually, this function will hold the execution of the code until it finishes adding records. However it is recommended that you add your Samples on set up for performance reasons.

>**NOTE**: Unlike other testing frameworks and libraries, PHPMetro will run the method `setUp` only once before running all tests instead of once before each test.

Now that there is Sample data we can start performing some calculations on it. For that you must simply just add Test methods in your class.

A *Test* is an special class method inside your Analysis class that performs some kind of calculation and returns the result. These methods must match the regular expression: `/test[A-Za-z09]*\(\)/` and return a basic data type.

>**WARNING**: Tests that return complex data types such as arrays or objects that can't be casted to strings will throw an error exception and stop the PHPMetro run.

For example, we want to test what's the median average of the generated random numbers:

```php
public function testMedianAverage()
{
    $sample = $this->getSample('RandomNumbers');

    $total = \count($sample);
    $sum = 0;

    foreach ($sample as $number)
    {
        $sum += $number;
    }

    return $sum / $total;
}
```

To calculate it we first accessed the sample we added on set up with `getSample`, and then counted the sum of the total values and the number of values. Your classic average.

You can keep writing more Tests to calculate different things with your Samples. Write Tests as you need them.

Finally, to run the Analyses just run the binary:

```console
$ ./vendor/bin/phpmetro
```

Your Tests results should start appearing on your console screen nested by Analysis class (assuming you configured your `phpmetro.xml` file properly).
