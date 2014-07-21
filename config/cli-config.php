<?php

use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once 'bootstrap.php';

// replace with mechanism to retrieve EntityManager in your app
$entityManager = LoadServiceEntityManager();
return ConsoleRunner::createHelperSet($entityManager);