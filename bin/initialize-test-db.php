<?php
use inklabs\kommerce\Entity\Address;
use inklabs\kommerce\Entity\Attribute;
use inklabs\kommerce\Entity\AttributeChoiceType;
use inklabs\kommerce\Entity\AttributeValue;
use inklabs\kommerce\Entity\Configuration;
use inklabs\kommerce\Entity\Image;
use inklabs\kommerce\Entity\Point;
use inklabs\kommerce\Entity\Product;
use inklabs\kommerce\Entity\ProductAttribute;
use inklabs\kommerce\Entity\Tag;
use inklabs\kommerce\Entity\TaxRate;
use inklabs\kommerce\Entity\User;
use inklabs\kommerce\Entity\UserRole;
use inklabs\kommerce\Entity\UserRoleType;
use inklabs\kommerce\Entity\Warehouse;

require_once __DIR__  . '/../config/cli-config.php';

unlink(__DIR__ . '/../storage/db.sqlite');
$classes = $entityManager->getMetaDataFactory()->getAllMetaData();
$tool = new Doctrine\ORM\Tools\SchemaTool($entityManager);
$tool->dropSchema($classes);
$tool->createSchema($classes);

$address = new Address();
$address->setAttention('Shipping Dept');
$address->setCompany('Acme Ltd');
$address->setAddress1('123 Any St');
$address->setAddress2('Ste 2');
$address->setCity('Santa Monica');
$address->setState('CA');
$address->setZip5('90405');
$address->setPoint(new Point(34.052234, -118.243685));
$warehouse = new Warehouse('Santa Monica - Main St', $address);

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
    $warehouse,
    $adminRole,
    $admin,
    $customer,
    TaxRate::createZip5('90405', 6.51, true),
    TaxRate::createZip5('76667', 5.5, false),
    TaxRate::createZip5Range('90201', '90301', 7.5, true),
    TaxRate::createZip5Range('73621', '73721', 7.9, true),
    TaxRate::createState('DC', 9.78, true),
    TaxRate::createState('CA', 8.5, true),
    new Configuration('storeTheme', 'Store Theme', 'foundation'),
    new Configuration('adminTheme', 'Admin Theme', 'cardinal'),
    new Configuration('easyPostApiKey', 'EasyPost API Key', 'xxx234'),
    new Configuration('stripeApiKey', 'Stripe API Key', 'xxyyzz'),
];

$sizeAttribute = new Attribute('Size', AttributeChoiceType::select(), 1);
$largeAttributeValue = new AttributeValue($sizeAttribute, 'Large', 1);
$mediumAttributeValue = new AttributeValue($sizeAttribute, 'Medium', 2);
$smallAttributeValue = new AttributeValue($sizeAttribute, 'Small', 3);

$colorAttribute = new Attribute('Color', AttributeChoiceType::imageLink(), 2);
$redAttributeValue = new AttributeValue($colorAttribute, 'Red', 1);
$blueAttributeValue = new AttributeValue($colorAttribute, 'Blue', 2);
$grayAttributeValue = new AttributeValue($colorAttribute, 'Gray', 2);
$greenAttributeValue = new AttributeValue($colorAttribute, 'Green', 3);
$orangeAttributeValue = new AttributeValue($colorAttribute, 'Orange', 4);

$largeTag = new Tag();
$largeTag->setIsVisible(true);
$largeTag->setName('Large Shirt');

$mediumTag = new Tag();
$mediumTag->setIsVisible(true);
$mediumTag->setName('Medium Shirt');

$smallTag = new Tag();
$smallTag->setIsVisible(true);
$smallTag->setName('Small Shirt');

addShirts('Red Shirt',    'SHT-RED', 'http://i.imgur.com/mFpzjL2.jpg', 550, 600, $redAttributeValue);
addShirts('Blue Shirt',   'SHT-BLU', 'http://i.imgur.com/3c68pg3.jpg', 550, 600, $blueAttributeValue);
addShirts('Gray Shirt',   'SHT-GRY', 'http://i.imgur.com/Wzc9lUc.jpg', 550, 600, $grayAttributeValue);
addShirts('Green Shirt',  'SHT-GRN', 'http://i.imgur.com/6oKbsLw.jpg', 550, 600, $greenAttributeValue);
addShirts('Orange Shirt', 'SHT-ONG', 'http://i.imgur.com/y7fSopr.jpg', 550, 600, $orangeAttributeValue);

$entities[] = $colorAttribute;
$entities[] = $largeTag;
$entities[] = $mediumTag;
$entities[] = $smallTag;

foreach ($entities as $entity) {
    /** Doctrine\ORM\EntityManager $entityManager */
    $entityManager->persist($entity);
}

$entityManager->flush();


function addShirts($name, $sku, $path, $width, $height, AttributeValue $colorAttributeValue)
{
    global $entities;
    global $sizeAttribute, $largeAttributeValue, $mediumAttributeValue, $smallAttributeValue, $colorAttribute;
    global $largeTag, $mediumTag, $smallTag;

    $image = new Image();
    $image->setPath($path);
    $image->setWidth($width);
    $image->setHeight($height);

    $largeProduct = getProduct('Large ' . $name, $sku . '-LG', 1000, $image);
    $mediumProduct = getProduct('Medium ' . $name, $sku . '-MD', 800, $image);
    $smallProduct = getProduct('Small ' . $name, $sku . '-SM', 600, $image);

    $sizeAttribute->addProductAttribute(new ProductAttribute($largeProduct, $largeAttributeValue));
    $sizeAttribute->addProductAttribute(new ProductAttribute($mediumProduct, $mediumAttributeValue));
    $sizeAttribute->addProductAttribute(new ProductAttribute($smallProduct, $smallAttributeValue));

    $colorAttribute->addProductAttribute(new ProductAttribute($largeProduct, $colorAttributeValue));
    $colorAttribute->addProductAttribute(new ProductAttribute($mediumProduct, $colorAttributeValue));
    $colorAttribute->addProductAttribute(new ProductAttribute($smallProduct, $colorAttributeValue));

    $tag = new Tag();
    $tag->setIsVisible(true);
    $tag->setName($name);
    $tag->addImage($image);
    $tag->addProduct($largeProduct);
    $tag->addProduct($mediumProduct);
    $tag->addProduct($smallProduct);

    $largeTag->addProduct($largeProduct);
    $mediumTag->addProduct($mediumProduct);
    $smallTag->addProduct($smallProduct);

    $entities = array_merge($entities, [
        $image,
        $largeProduct,
        $mediumProduct,
        $smallProduct,
        $tag,
        $sizeAttribute
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
