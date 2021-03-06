<?php

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

require_once 'config/config.php';

//error_reporting(E_ALL & ~E_NOTICE);
error_reporting(E_ALL);

/**
 * @return EntityManager
 */
function LoadServiceEntityManager() {
    $config = Setup::createAnnotationMetadataConfiguration(
        array(
            APP_PATH.'includes/Entity'
        ),
        APP_ENVIRONMENT=='development',
        APP_PATH . 'cache/doctrine/',
        null,
        false
    );
    return EntityManager::create(
        array(
            'driver'   => DB_DRIVER,
            'host'     => DB_HOST,
            'dbname'   => DB_NAME,
            'user'     => DB_USER,
            'password' => DB_PASS,
        ),
        $config
    );
}