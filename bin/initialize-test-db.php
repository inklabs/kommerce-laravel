<?php
use inklabs\kommerce\Entity\Attribute;
use inklabs\kommerce\Entity\AttributeValue;
use inklabs\kommerce\Entity\Image;
use inklabs\kommerce\Entity\Product;
use inklabs\kommerce\Entity\ProductAttribute;
use inklabs\kommerce\Entity\Tag;
use inklabs\kommerce\Entity\TaxRate;
use inklabs\kommerce\Entity\User;
use inklabs\kommerce\Entity\UserRole;
use inklabs\kommerce\Entity\UserRoleType;

require_once __DIR__  . '/../config/cli-config.php';

unlink(__DIR__ . '/../storage/db.sqlite');
$classes = $entityManager->getMetaDataFactory()->getAllMetaData();
$tool = new Doctrine\ORM\Tools\SchemaTool($entityManager);
$tool->dropSchema($classes);
$tool->createSchema($classes);


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

$attribute = new Attribute('Size', 1);
$attributeValueLarge = new AttributeValue($attribute, 'Large', 1);
$attributeValueMedium = new AttributeValue($attribute, 'Medium', 2);
$attributeValueSmall = new AttributeValue($attribute, 'Small', 3);

addShirts('Red Shirt',    'SHT-RED', 'http://i.imgur.com/mFpzjL2.jpg', 550, 600);
addShirts('Blue Shirt',   'SHT-BLU', 'http://i.imgur.com/3c68pg3.jpg', 550, 600);
addShirts('Gray Shirt',   'SHT-GRY', 'http://i.imgur.com/Wzc9lUc.jpg', 550, 600);
addShirts('Green Shirt',  'SHT-GRN', 'http://i.imgur.com/6oKbsLw.jpg', 550, 600);
addShirts('Orange Shirt', 'SHT-ONG', 'http://i.imgur.com/y7fSopr.jpg', 550, 600);

foreach ($entities as $entity) {
    /** Doctrine\ORM\EntityManager $entityManager */
    $entityManager->persist($entity);
}

$entityManager->flush();


function addShirts($name, $sku, $path, $width, $height)
{
    global $entities, $attribute, $attributeValueLarge, $attributeValueMedium, $attributeValueSmall;

    $image = new Image();
    $image->setPath($path);
    $image->setWidth($width);
    $image->setHeight($height);

    $large = getProduct('Large ' . $name, $sku . '-LG', 1000, $image);
    $medium = getProduct('Medium ' . $name, $sku . '-MD', 800, $image);
    $small = getProduct('Small ' . $name, $sku . '-SM', 600, $image);

    $attribute->addProductAttribute(new ProductAttribute($large, $attributeValueLarge));
    $attribute->addProductAttribute(new ProductAttribute($medium, $attributeValueMedium));
    $attribute->addProductAttribute(new ProductAttribute($small, $attributeValueSmall));

    $tag = new Tag();
    $tag->setName($name);
    $tag->addImage($image);
    $tag->addProduct($large);
    $tag->addProduct($medium);
    $tag->addProduct($small);

    $entities = array_merge($entities, [
        $image,
        $large,
        $medium,
        $small,
        $tag,
        $attribute
    ]);
}

function getProduct($name, $sku, $unitPrice, Image $image)
{
    $product = new Product();
    $product->setSku($sku);
    $product->setName($name);
    $product->setUnitPrice($unitPrice);
    $product->setIsActive(true);
    $product->setIsVisible(true);
    $product->setDefaultImage($image->getPath());

    return $product;
}
