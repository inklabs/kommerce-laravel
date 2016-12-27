<?php
namespace App\Lib;

use Doctrine\Common\Cache\ArrayCache;
use inklabs\kommerce\EntityDTO\Builder\DTOBuilderFactory;
use inklabs\kommerce\EntityDTO\OrderAddressDTO;
use inklabs\kommerce\EntityRepository\RepositoryFactory;
use inklabs\kommerce\Lib\CartCalculator;
use inklabs\kommerce\Lib\Command\CommandBus;
use inklabs\kommerce\Lib\Command\CommandInterface;
use inklabs\kommerce\Lib\DoctrineHelper;
use inklabs\kommerce\Lib\Event\EventDispatcher;
use inklabs\kommerce\Lib\LocalFileManager;
use inklabs\kommerce\Lib\Mapper;
use inklabs\kommerce\Lib\PaymentGateway\FakePaymentGateway;
use inklabs\kommerce\Lib\PaymentGateway\Stripe;
use inklabs\kommerce\Lib\Pricing;
use inklabs\kommerce\Lib\Query\QueryBus;
use inklabs\kommerce\Lib\Query\QueryInterface;
use inklabs\kommerce\Lib\ShipmentGateway\EasyPostGateway;
use inklabs\kommerce\Service\ServiceFactory;
use inklabs\kommerce\tests\Helper\Lib\ShipmentGateway\FakeShipmentGateway;

class KommerceConfiguration
{
    public function dispatch(CommandInterface $command)
    {
        $this->getCommandBus()->execute($command);
    }

    public function dispatchQuery(QueryInterface $query)
    {
        $this->getQueryBus()->execute($query);
    }

    public function getPricing()
    {
        static $pricing = null;
        if ($pricing === null) {
            $pricing = new Pricing();
            $pricing->loadCatalogPromotions(
                $this->getRepositoryFactory()->getCatalogPromotionRepository()
            );
        }
        return $pricing;
    }

    public function getCartCalculator()
    {
        static $cartCalculator = null;
        if ($cartCalculator === null) {
            $cartCalculator = new CartCalculator(
                $this->getPricing()
            );
        }
        return $cartCalculator;
    }

    public function getDTOBuilderFactory()
    {
        static $DTOBuilderFactory = null;
        if ($DTOBuilderFactory === null) {
            $DTOBuilderFactory = new DTOBuilderFactory();
        }
        return $DTOBuilderFactory;
    }

    private function getServiceFactory()
    {
        static $serviceFactory = null;
        if ($serviceFactory === null) {
            $serviceFactory = new ServiceFactory(
                $this->getRepositoryFactory(),
                $this->getCartCalculator(),
                $this->getEventDispatcher(),
                $this->getPaymentGateway(),
                $this->getShipmentGateway(),
                $this->getFileManage()
            );
        }
        return $serviceFactory;
    }

    private function getCommandBus()
    {
        static $commandBus = null;
        if ($commandBus === null) {
            $commandBus = new CommandBus(
                $this->getMapper()
            );
        }
        return $commandBus;
    }

    private function getQueryBus()
    {
        static $queryBus = null;
        if ($queryBus === null) {
            $queryBus = new QueryBus(
                $this->getMapper()
            );
        }
        return $queryBus;
    }

    private function getCacheDriver()
    {
        static $cacheDriver = null;
        if ($cacheDriver === null) {
            $cacheDriver = new ArrayCache();
        }
        return $cacheDriver;
    }

    private function getMapper()
    {
        static $mapper = null;
        if ($mapper === null) {
            $mapper = new Mapper(
                $this->getRepositoryFactory(),
                $this->getServiceFactory(),
                $this->getPricing(),
                $this->getDTOBuilderFactory()
            );
        }
        return $mapper;
    }

    private function getRepositoryFactory()
    {
        static $repositoryFactory = null;
        if ($repositoryFactory === null) {
            $repositoryFactory = new RepositoryFactory(
                $this->getEntityManager()
            );
        }
        return $repositoryFactory;
    }

    private function getEntityManager()
    {
        static $entityManager = null;
        if ($entityManager === null) {
            $cacheDriver = $this->getCacheDriver();
            $doctrineHelper = new DoctrineHelper($cacheDriver);
            $doctrineHelper->setup([
                'driver' => 'pdo_sqlite',
                'path' => storage_path() . '/db.sqlite',
            ]);
            $doctrineHelper->addSqliteFunctions();

            $entityManager = $doctrineHelper->getEntityManager();
        }
        return $entityManager;
    }

    private function getEventDispatcher()
    {
        static $eventDispatcher = null;
        if ($eventDispatcher === null) {
            $eventDispatcher = new EventDispatcher();
        }
        return $eventDispatcher;
    }

    private function getPaymentGateway()
    {
        static $paymentGateway = null;
        if ($paymentGateway === null) {
            $stripeApiKey = env('STRIPE-API-KEY');
            if ($stripeApiKey === 'your-key-here') {
                $paymentGateway = new FakePaymentGateway();
            } else {
                $paymentGateway = new Stripe($stripeApiKey);
            }
        }
        return $paymentGateway;
    }

    private function getShipmentGateway()
    {
        static $shipmentGateway = null;
        if ($shipmentGateway === null) {
            $storeAddress = new OrderAddressDTO();
            $storeAddress->zip5 = '90401';

            $easyPostApiKey = env('EASYPOST-API-KEY');
            if ($easyPostApiKey === 'your-key-here') {
                // TODO: This is crossing the kommerce-core/test namespace boundary
                $shipmentGateway = new FakeShipmentGateway($storeAddress);
            } else {
                $shipmentGateway = new EasyPostGateway(
                    $easyPostApiKey,
                    $storeAddress
                );
            }
        }
        return $shipmentGateway;
    }

    private function getFileManage()
    {
        static $fileManager = null;
        if ($fileManager === null) {
            $fileManager = new LocalFileManager(
                storage_path() . '/files'
            );
        }
        return $fileManager;
    }
}
