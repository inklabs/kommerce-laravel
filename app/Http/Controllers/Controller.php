<?php

namespace App\Http\Controllers;

use App\Lib\CSRFTokenGenerator;
use App\Lib\LaravelRouteUrl;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\ORM\EntityManager;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use inklabs\kommerce\Action\Cart\CreateCartCommand;
use inklabs\kommerce\Action\Cart\GetCartBySessionIdQuery;
use inklabs\kommerce\Action\Cart\GetCartQuery;
use inklabs\kommerce\Action\Cart\Query\GetCartBySessionIdRequest;
use inklabs\kommerce\Action\Cart\Query\GetCartBySessionIdResponse;
use inklabs\kommerce\Action\Cart\Query\GetCartRequest;
use inklabs\kommerce\Action\Cart\Query\GetCartResponse;
use inklabs\kommerce\Action\Product\GetRandomProductsQuery;
use inklabs\kommerce\Action\Product\Query\GetRandomProductsRequest;
use inklabs\kommerce\Action\Product\Query\GetRandomProductsResponse;
use inklabs\kommerce\EntityDTO\Builder\DTOBuilderFactory;
use inklabs\kommerce\EntityDTO\Builder\DTOBuilderFactoryInterface;
use inklabs\kommerce\EntityDTO\CartDTO;
use inklabs\kommerce\EntityDTO\OrderAddressDTO;
use inklabs\kommerce\EntityDTO\PaginationDTO;
use inklabs\kommerce\EntityDTO\UserDTO;
use inklabs\kommerce\EntityRepository\RepositoryFactory;
use inklabs\kommerce\Exception\EntityNotFoundException;
use inklabs\kommerce\Lib\CartCalculator;
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
use inklabs\kommerce\Lib\Pricing;
use inklabs\kommerce\Lib\Query\QueryBus;
use inklabs\kommerce\Lib\Query\QueryBusInterface;
use inklabs\kommerce\Lib\Query\QueryInterface;
use inklabs\kommerce\Lib\ShipmentGateway\EasyPostGateway;
use inklabs\kommerce\Lib\ShipmentGateway\ShipmentGatewayInterface;
use inklabs\kommerce\Service\ServiceFactory;
use inklabs\kommerce\tests\Helper\Lib\ShipmentGateway\FakeShipmentGateway;
use inklabs\KommerceTemplates\Lib\AssetLocationService;
use inklabs\KommerceTemplates\Lib\TwigTemplate;
use inklabs\KommerceTemplates\Lib\TwigThemeConfig;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Twig_Environment;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    /** @var DoctrineHelper */
    private $doctrineHelper;

    /** @var Pricing */
    private $pricing;

    /** @var CartCalculator */
    private $cartCalculator;

    /** @var EntityManager */
    private $entityManager;

    /** @var CommandBusInterface */
    private $commandBus;

    /** @var QueryBusInterface */
    private $queryBus;

    /** @var MapperInterface */
    private $mapper;

    /** @var RepositoryFactory */
    private $repositoryFactory;

    /** @var ServiceFactory */
    private $serviceFactory;

    /** @var DTOBuilderFactoryInterface */
    private $DTOBuilderFactory;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var PaymentGatewayInterface */
    private $paymentGateway;

    /** @var  ShipmentGatewayInterface */
    private $shipmentGateway;

    /** @var FileManagerInterface */
    private $fileManager;

    /** @var CartDTO */
    private $cartDTO;

    /** @var UserDTO */
    private $userDTO;

    public function __construct()
    {
        $this->setupKommerce();
    }

    protected function getPricing()
    {
        return $this->pricing;
    }

    protected function getCartCalculator()
    {
        return $this->cartCalculator;
    }

    protected function dispatch(CommandInterface $command)
    {
        $this->commandBus->execute($command);
    }

    protected function dispatchQuery(QueryInterface $query)
    {
        $this->queryBus->execute($query);
    }

    public function getDTOBuilderFactory()
    {
        return $this->DTOBuilderFactory;
    }

    private function setupKommerce()
    {
        $this->setupDoctrine();
        $this->setupRepositoryFactory();
        $this->setupPricing();
        $this->setupEventDispatcher();
        $this->setupPaymentGateway();
        $this->setupFileManager();
        $this->setupShipmentGateway();
        $this->setupServiceFactory();
        $this->setupDTOBuilderFactory();
        $this->setupMapper();
        $this->setupCommandBus();
        $this->setupQueryBus();
    }

    private function setupDoctrine()
    {
        $cacheDriver = $this->getCacheDriver();
        $this->doctrineHelper = new DoctrineHelper($cacheDriver);
        $this->doctrineHelper->setup([
            'driver' => 'pdo_sqlite',
            'path' => storage_path() . '/db.sqlite',
        ]);
        $this->doctrineHelper->addSqliteFunctions();

        $this->entityManager = $this->doctrineHelper->getEntityManager();
    }

    private function getCacheDriver()
    {
        return new ArrayCache();
    }

    private function setupPricing()
    {
        $this->pricing = new Pricing();
        $this->pricing->loadCatalogPromotions($this->repositoryFactory->getCatalogPromotionRepository());
        $this->cartCalculator = new CartCalculator($this->pricing);
    }

    private function setupRepositoryFactory()
    {
        $this->repositoryFactory = new RepositoryFactory(
            $this->entityManager
        );
    }

    private function setupEventDispatcher()
    {
        $this->eventDispatcher = new EventDispatcher();
    }

    private function setupPaymentGateway()
    {
        $this->paymentGateway = new FakePaymentGateway();
        // TODO: Add Stripe payment gateway
        //$this->paymentGateway = new Stripe(env('STRIPE-API-KEY'));
    }

    private function setupFileManager()
    {
        $this->fileManager = new LocalFileManager(
            storage_path() . '/files'
        );
    }

    private function setupShipmentGateway()
    {
        $storeAddress = new OrderAddressDTO();
        $storeAddress->zip5 = '90401';

        $easypostApiKey = env('EASYPOST-API-KEY');
        if ($easypostApiKey === 'your-key-here') {
            // TODO: This is crossing the kommerce-core/test namespace boundary
            $this->shipmentGateway = new FakeShipmentGateway($storeAddress);
        } else {
            $this->shipmentGateway = new EasyPostGateway(
                env('EASYPOST-API-KEY'),
                $storeAddress
            );
        }
    }

    private function setupServiceFactory()
    {
        $this->serviceFactory = new ServiceFactory(
            $this->repositoryFactory,
            $this->cartCalculator,
            $this->eventDispatcher,
            $this->paymentGateway,
            $this->shipmentGateway,
            $this->fileManager
        );
    }

    private function setupDTOBuilderFactory()
    {
        $this->DTOBuilderFactory = new DTOBuilderFactory();
    }

    private function setupMapper()
    {
        $this->mapper = new Mapper(
            $this->serviceFactory,
            $this->pricing,
            $this->DTOBuilderFactory
        );
    }

    private function setupCommandBus()
    {
        $this->commandBus = new CommandBus(
            $this->mapper
        );
    }

    private function setupQueryBus()
    {
        $this->queryBus = new QueryBus(
            $this->mapper
        );
    }

    /**
     * @return string
     */
    protected function getRemoteIP4()
    {
        return request()->ip();
    }

    /**
     * @return string
     */
    private function getSessionId()
    {
        return session()->getId();
    }

    /**
     * @return \Illuminate\Session\Store
     */
    private function getSession()
    {
        return session();
    }

    /**
     * @return CartDTO
     */
    protected function getCart()
    {
        $cartId = $this->getCartId();

        $request = new GetCartRequest($cartId);
        $response = new GetCartResponse($this->getCartCalculator());
        $this->dispatchQuery(new GetCartQuery($request, $response));

        return $response->getCartDTOWithAllData();
    }

    /**
     * @return string
     */
    protected function getCartId()
    {
        if ($this->cartDTO != null) {
            return $this->cartDTO->id->getHex();
        }

        try {
            $cartDTO = $this->getCartFromSession();
            return $cartDTO->id->getHex();
        } catch (EntityNotFoundException $e) {
        }

        $this->createNewCart();

        return $this->cartDTO->id->getHex();
    }

    /**
     * @return CartDTO
     * @throws EntityNotFoundException
     */
    private function getCartFromSession()
    {
        $request = new GetCartBySessionIdRequest($this->getSessionId());
        $response = new GetCartBySessionIdResponse($this->cartCalculator);
        $this->dispatchQuery(new GetCartBySessionIdQuery($request, $response));

        return $response->getCartDTO();
    }

    protected function createNewCart()
    {
        $userId = null;
        if ($this->userDTO !== null) {
            $userId = $this->userDTO->id->getHex();
        }

        $createCartCommand = new CreateCartCommand(
            $this->getRemoteIP4(),
            $userId,
            $this->getSessionId()
        );
        $this->dispatch($createCartCommand);
        $cartId = $createCartCommand->getCartId();

        $request = new GetCartRequest($cartId);
        $response = new GetCartResponse($this->getCartCalculator());
        $this->dispatchQuery(new GetCartQuery($request, $response));

        $this->cartDTO = $response->getCartDTO();
    }

    /**
     * @param $name
     * @param array $context
     * @return \Illuminate\Contracts\Routing\ResponseFactory | \Symfony\Component\HttpFoundation\Response
     */
    protected function renderTemplate($name, $context = [])
    {
        $twig = $this->getTwigTemplate();

        $this->setGlobalFlashVariables($twig);

        return response(
            $twig->render($name, $context)
        );
    }

    private function setGlobalFlashVariables(TwigTemplate $twig)
    {
        $session = $this->getSession();
        if ($session->isStarted()) {
            $flashValues = [
                'flashMessages',
                'templateFlashMessages',
                'flashFormErrors',
            ];

            foreach ($flashValues as $flashValue) {
                if ($session->has($flashValue)) {
                    $twig->addGlobal($flashValue, $session->get($flashValue));
                    $session->forget($flashValue);
                }
            }
        }
    }

    /**
     * @return TwigTemplate
     */
    protected function getTwigTemplate()
    {
        $twigTemplate = new TwigTemplate(
            $this->getThemeConfig(),
            new CSRFTokenGenerator(),
            new LaravelRouteUrl(),
            env('STORE_TIMEZONE'),
            env('STORE_DATE_FORMAT'),
            env('STORE_TIME_FORMAT')
        );
        $twigTemplate->enableDebug();

        return $twigTemplate;
    }

    /**
     * @param string $message
     */
    protected function flashSuccess($message = '')
    {
        $this->flashMessage('success', $message);
    }

    /**
     * @param string $flashTemplate
     * @param array $data
     */
    protected function flashTemplateSuccess($flashTemplate, array $data)
    {
        $this->flashTemplateMessage('success', $flashTemplate, $data);
    }

    /**
     * @param string $message
     */
    protected function flashError($message = '')
    {
        $this->flashMessage('danger', $message);
    }

    /**
     * @param string $message
     */
    protected function flashWarning($message = '')
    {
        $this->flashMessage('warning', $message);
    }

    public function flashGenericWarning($message = 'Something went wrong.')
    {
        $extraMessage = 'Please contact us at store@example.com';
        $this->flashWarning($message . ' ' . $extraMessage);
    }

    /**
     * @param string $type
     * @param string $message
     */
    private function flashMessage($type, $message = '')
    {
        $messages = session()->get('flashMessages', []);
        $messages[$type][] = $message;
        session()->flash('flashMessages', $messages);
    }

    protected function flashFormErrors(ConstraintViolationListInterface $newFormErrors)
    {
        /** @var ConstraintViolationListInterface | ConstraintViolationInterface[] $formErrors */
        $formErrors = session()->get('flashFormErrors', new ConstraintViolationList());
        $formErrors->addAll($newFormErrors);
        session()->flash('flashFormErrors', $formErrors);
    }

    /**
     * @param string $type
     * @param string $flashTemplate
     * @param array $data
     */
    private function flashTemplateMessage($type, $flashTemplate, array $data)
    {
        $messages = session()->get('templateFlashMessages', []);
        $messages[$type][] = [
            'flashTemplate' => $flashTemplate,
            'data' => $data,
        ];
        session()->flash('templateFlashMessages', $messages);
    }

    /**
     * @param int $maxResults
     * @return PaginationDTO
     */
    protected function getPaginationDTO($maxResults)
    {
        $page = request()->query('page');
        if (empty($page)) {
            $page = 1;
        }
        $paginationDTO = new PaginationDTO;
        $paginationDTO->maxResults = $maxResults;
        $paginationDTO->page = $page;
        $paginationDTO->isTotalIncluded = true;

        return $paginationDTO;
    }

    protected function getAssetLocationService()
    {
        $assetLocationService = new AssetLocationService();
        return $assetLocationService;
    }

    protected function getRandomProducts($limit)
    {
        $request = new GetRandomProductsRequest($limit);
        $response = new GetRandomProductsResponse($this->getPricing());
        $this->dispatchQuery(new GetRandomProductsQuery($request, $response));

        return $response->getProductDTOs();
    }

    /**
     * @return TwigThemeConfig
     */
    protected function getThemeConfig()
    {
        $themePath = TwigThemeConfig::getThemePath(env('THEME'));
        $themeConfig = TwigThemeConfig::loadConfig($themePath);
        return $themeConfig;
    }
}
