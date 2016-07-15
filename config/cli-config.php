<?php
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use inklabs\kommerce\Lib\DoctrineHelper;

require_once __DIR__ . '/../vendor/autoload.php';

$doctrineHelper = new DoctrineHelper(new Doctrine\Common\Cache\ArrayCache());
$doctrineHelper->setup([
    'driver' => 'pdo_sqlite',
    'path' => __DIR__ . '/../storage/db.sqlite',
]);

$entityManager = $doctrineHelper->getEntityManager();

// Fix MySQL enum
$platform = $entityManager->getConnection()->getDatabasePlatform();
$platform->registerDoctrineTypeMapping('enum', 'string');

return ConsoleRunner::createHelperSet($entityManager);
