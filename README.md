Pyrowman/Pheanstalk
==========


Pheanstalk is a pure PHP 5.3+ client for the [evqueue workqueue][1].  It has
been actively developed, and used in production by many, since late 2008.

Pheanstalk 3.0 introduces PHP namespaces, PSR-1 and PSR-2 coding standards,
and PSR-4 autoloader standard.

This project is a fork from the [original Pheanstalk][3] wich is a client for the beanstalkd workqueue.  
Special thanks to [Paul Annesley][2]; the original creator of the library.

  [1]: https://github.com/coldsource/evqueue-core
  [2]: http://paul.annesley.cc/
  [3]: https://github.com/pheanstalk/pheanstalk

Installation with Composer
-------------

Install pheanstalk as a dependency with composer:

```bash
composer require pyrowman/pheanstalk
```


Usage Example
-------------

```php
<?php

// Hopefully you're using Composer autoloading.

use Pheanstalk\Pheanstalk;

$pheanstalk = new Pheanstalk('127.0.0.1', 'admin', 'admin');


// Create a simple Worflow with one job inside

$workflow = $pheanstalk->createTask('Sleep', 'Test', '/bin/sleep 80');

// Put the job into instance execution

$pheanstalk->put($workflow);

// ----------------------------------------
// check server availability

$pheanstalk->getConnection()->isServiceListening(); // true or false

//-----------------------------------------
// Add a scheduler for the job (by default in continous)

$workflowSchedule = $pheanstalk->createSchedule($workflow, new TimeSchedule());

//-----------------------------------------
// Getting infos on the execution of a workflow

$workflowInstances = $pheanstalk->getWorkflowInstances($workflow);

//-----------------------------------------
// Delete a job 

if ($workflow = $pheanstalk->workflowExists('Sleep'))
    $pheanstalk->delete($workflow);

```


Running the tests
-----------------

There is a section of the test suite which depends on a running beanstalkd
at 127.0.0.1:5000, which was previously opt-in via `--with-server`.
Since porting to PHPUnit, all tests are run at once. Feel free to submit
a pull request to rectify this.

```
# ensure you have Composer set up
$ wget http://getcomposer.org/composer.phar
$ php composer.phar install

$ ./vendor/bin/phpunit
PHPUnit 4.0.19 by Sebastian Bergmann.

Configuration read from /Users/pda/code/pheanstalk/phpunit.xml.dist

................................................................. 65 / 83 ( 78%)
..................

Time: 239 ms, Memory: 6.00Mb

OK (83 tests, 378 assertions)
```

License
-------

© Valentin Corre

Released under the [The MIT License](http://www.opensource.org/licenses/mit-license.php)
