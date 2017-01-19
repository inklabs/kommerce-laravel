<?php
use Doctrine\ORM\Query\ResultSetMapping;
use inklabs\kommerce\Entity\TaxRate;
use inklabs\kommerce\Entity\User;
use inklabs\kommerce\Entity\UserRole;
use inklabs\kommerce\Entity\UserRoleType;

require_once __DIR__  . '/../config/cli-config.php';

$adminRole = new UserRole(UserRoleType::admin());

$admin = new User();
$admin->setFirstName('Aaron');
$admin->setLastName('Admin');
$admin->setEmail('aaron@example.com');
$admin->setPassword('Test123!');
$admin->addUserRole($adminRole);

$customer = new User();
$customer->setFirstName('Charles');
$customer->setLastName('Customer');
$customer->setEmail('charles@example.com');
$customer->setPassword('Test123!');

$entities = [
    $adminRole,
    $admin,
    $customer,
    TaxRate::createZip5('90405', 6.51, true),
    TaxRate::createZip5('76667', 5.5, false),
    TaxRate::createZip5Range('90201', '90301', 7.5, true),
    TaxRate::createZip5Range('73621', '73721', 7.9, true),
    TaxRate::createState('DC', 9.78, true),
    TaxRate::createState('CA', 8.5, true),
];

$truncateEntities = [
    'zk_Order',
    'zk_TaxRate',
    'zk_User',
    'zk_UserRole',
    'zk_user_userrole',
];

// TODO: Delete sqlite database file instead of truncating
foreach ($truncateEntities as $class) {
    $entityManager->createNativeQuery('DELETE FROM ' . $class, new ResultSetMapping())
        ->execute();
}

//$entityManager->createNativeQuery('PRAGMA foreign_keys = ON', new ResultSetMapping())->execute();

foreach ($entities as $entity) {
    /** Doctrine\ORM\EntityManager $entityManager */
    $entityManager->persist($entity);
    $entityManager->flush();
}
