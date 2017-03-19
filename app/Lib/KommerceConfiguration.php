<?php
namespace App\Lib;

use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManagerInterface;
use inklabs\kommerce\EntityDTO\Builder\DTOBuilderFactory;
use inklabs\kommerce\EntityDTO\Builder\DTOBuilderFactoryInterface;
use inklabs\kommerce\EntityDTO\OrderAddressDTO;
use inklabs\kommerce\EntityRepository\RepositoryFactory;
use inklabs\kommerce\Lib\Authorization\AuthorizationContextInterface;
use inklabs\kommerce\Lib\Authorization\SessionAuthorizationContext;
use inklabs\kommerce\Lib\CartCalculator;
use inklabs\kommerce\Lib\CartCalculatorInterface;
use inklabs\kommerce\Lib\Command\CommandBus;
use inklabs\kommerce\Lib\Command\CommandBusInterface;
use inklabs\kommerce\Lib\Command\CommandInterface;
use inklabs\kommerce\Lib\DoctrineHelper;
use inklabs\kommerce\Lib\Event\EventDispatcher;
use inklabs\kommerce\Lib\Event\EventDispatcherInterface;
use inklabs\kommerce\Lib\FileManagerInterface;
use inklabs\kommerce\Lib\LocalFileManager;
use inklabs\kommerce\Lib\Mapper;
use inklabs\kommerce\Lib\MapperInterface;
use inklabs\kommerce\Lib\PaymentGateway\FakePaymentGateway;
use inklabs\kommerce\Lib\PaymentGateway\PaymentGatewayInterface;
use inklabs\kommerce\Lib\PaymentGateway\Stripe;
use inklabs\kommerce\Lib\Pricing;
use inklabs\kommerce\Lib\PricingInterface;
use inklabs\kommerce\Lib\Query\QueryBus;
use inklabs\kommerce\Lib\Query\QueryBusInterface;
use inklabs\kommerce\Lib\Query\QueryInterface;
use inklabs\kommerce\Lib\ShipmentGateway\EasyPostGateway;
use inklabs\kommerce\Lib\ShipmentGateway\ShipmentGatewayInterface;
use inklabs\kommerce\Lib\UuidInterface;
use inklabs\kommerce\Service\ServiceFactory;
use inklabs\kommerce\tests\Helper\Lib\ShipmentGateway\FakeShipmentGateway;

class KommerceConfiguration
{
    /** @var null|UuidInterface */
    private $userId;

    /** @var null|string */
    private $sessionId;

    /** @var bool */
    private $isAdmin;

    /** @var AuthorizationContextInterface */
    private $authorizationContext;

    /** @var QueryBusInterface */
    private $queryBus;

    /** @var CommandBusInterface */
    private $commandBus;

    /** @var ServiceFactory */
    private $serviceFactory;

    /** @var \Doctrine\Common\Cache\CacheProvider */
    private $cacheDriver;

    /** @var MapperInterface */
    private $mapper;

    /** @var RepositoryFactory */
    private $repositoryFactory;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var PaymentGatewayInterface */
    private $paymentGateway;

    /** @var ShipmentGatewayInterface */
    private $shipmentGateway;

    /** @var FileManagerInterface */
    private $fileManager;

    /** @var DTOBuilderFactoryInterface */
    private $DTOBuilderFactory;

    /** @var CartCalculatorInterface */
    private $cartCalculator;

    /** @var PricingInterface */
    private $pricing;

    /**
     * @param UuidInterface|null $userId
     * @param null|string $sessionId
     * @param bool $isAdmin
     */
    public function __construct(UuidInterface $userId = null, $sessionId = null, $isAdmin = false)
    {
        $this->userId = $userId;
        $this->sessionId = $sessionId;
        $this->isAdmin = $isAdmin;
    }

    public function setUserId(UuidInterface $userId)
    {
        $this->userId = $userId;
    }

    public function dispatch(CommandInterface $command)
    {
        $this->getCommandBus()->execute($command);
    }

    public function dispatchQuery(QueryInterface $query)
    {
        return $this->getQueryBus()->execute($query);
    }

    public function getPricing()
    {
        if ($this->pricing === null) {
            $this->pricing = new Pricing();
            $this->pricing->loadCatalogPromotions(
                $this->getRepositoryFactory()->getCatalogPromotionRepository()
            );
        }
        return $this->pricing;
    }

    public function getCartCalculator()
    {
        if ($this->cartCalculator === null) {
            $this->cartCalculator = new CartCalculator(
                $this->getPricing()
            );
        }
        return $this->cartCalculator;
    }

    public function getDTOBuilderFactory()
    {
        if ($this->DTOBuilderFactory === null) {
            $this->DTOBuilderFactory = new DTOBuilderFactory();
        }
        return $this->DTOBuilderFactory;
    }

    private function getServiceFactory()
    {
        if ($this->serviceFactory === null) {
            $this->serviceFactory = new ServiceFactory(
                $this->getRepositoryFactory(),
                $this->getCartCalculator(),
                $this->getEventDispatcher(),
                $this->getPaymentGateway(),
                $this->getShipmentGateway(),
                $this->getFileManage()
            );
        }
        return $this->serviceFactory;
    }

    private function getCommandBus()
    {
        if ($this->commandBus === null) {
            $this->commandBus = new CommandBus(
                $this->getAuthorizationContext(),
                $this->getMapper(),
                $this->getEventDispatcher()
            );
        }
        return $this->commandBus;
    }

    private function getQueryBus()
    {
        if ($this->queryBus === null) {
            $this->queryBus = new QueryBus(
                $this->getAuthorizationContext(),
                $this->getMapper()
            );
        }
        return $this->queryBus;
    }

    private function getAuthorizationContext()
    {
        if ($this->authorizationContext === null) {
            $this->authorizationContext = new SessionAuthorizationContext(
                $this->getRepositoryFactory()->getCartRepository(),
                $this->getRepositoryFactory()->getOrderRepository(),
                $this->sessionId,
                $this->userId,
                $this->isAdmin
            );
        }
        return $this->authorizationContext;
    }

    private function getCacheDriver()
    {
        if ($this->cacheDriver === null) {
            $this->cacheDriver = new ArrayCache();
        }
        return $this->cacheDriver;
    }

    private function getMapper()
    {
        if ($this->mapper === null) {
            $this->mapper = new Mapper(
                $this->getRepositoryFactory(),
                $this->getServiceFactory(),
                $this->getPricing(),
                $this->getDTOBuilderFactory()
            );
        }
        return $this->mapper;
    }

    private function getRepositoryFactory()
    {
        if ($this->repositoryFactory === null) {
            $this->repositoryFactory = new RepositoryFactory(
                $this->getEntityManager()
            );
        }
        return $this->repositoryFactory;
    }

    private function getEntityManager()
    {
        if ($this->entityManager === null) {
            $cacheDriver = $this->getCacheDriver();
            $doctrineHelper = new DoctrineHelper($cacheDriver);
            $doctrineHelper->setup([
                'driver' => 'pdo_sqlite',
                'path' => storage_path() . '/db.sqlite',
            ]);
            $doctrineHelper->addSqliteFunctions();

            $this->entityManager = $doctrineHelper->getEntityManager();
        }
        return $this->entityManager;
    }

    private function getEventDispatcher()
    {
        if ($this->eventDispatcher === null) {
            $this->eventDispatcher = new EventDispatcher();
        }
        return $this->eventDispatcher;
    }

    private function getPaymentGateway()
    {
        if ($this->paymentGateway === null) {
            $stripeApiKey = env('STRIPE-API-KEY');
            if ($stripeApiKey === 'your-key-here') {
                $this->paymentGateway = new FakePaymentGateway();
            } else {
                $this->paymentGateway = new Stripe($stripeApiKey);
            }
        }
        return $this->paymentGateway;
    }

    private function getShipmentGateway()
    {
        if ($this->shipmentGateway === null) {
            $storeAddress = $this->getStoreAddress();

            $easyPostApiKey = env('EASYPOST-API-KEY');
            if ($easyPostApiKey === 'your-key-here') {
                // TODO: This is crossing the kommerce-core/test namespace boundary
                $this->shipmentGateway = new FakeShipmentGateway($storeAddress);
            } else {
                $this->shipmentGateway = new EasyPostGateway(
                    $easyPostApiKey,
                    $storeAddress
                );
            }
        }
        return $this->shipmentGateway;
    }

    private function getFileManage()
    {
        if ($this->fileManager === null) {
            $this->fileManager = new LocalFileManager(
                storage_path() . '/files'
            );
        }
        return $this->fileManager;
    }

    /**
     * @return OrderAddressDTO
     */
    public function getStoreAddress()
    {
        $storeAddress = new OrderAddressDTO();
        $storeAddress->firstName = env('STORE-FIRST-NAME');
        $storeAddress->lastName = env('STORE-LAST-NAME');
        $storeAddress->company = env('STORE-COMPANY');
        $storeAddress->address1 = env('STORE-ADDRESS1');
        $storeAddress->address2 = env('STORE-ADDRESS2');
        $storeAddress->city = env('STORE-CITY');
        $storeAddress->state = env('STORE-STATE');
        $storeAddress->zip5 = env('STORE-ZIP5');
        return $storeAddress;
    }
}
