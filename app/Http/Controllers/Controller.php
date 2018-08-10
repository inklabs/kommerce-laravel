<?php
namespace App\Http\Controllers;

use App\Lib\Arr;
use App\Lib\CSRFTokenGenerator;
use App\Lib\KommerceConfiguration;
use App\Lib\LaravelRouteUrl;
use DateTime;
use DateTimeZone;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use inklabs\kommerce\Action\Attribute\GetAttributeQuery;
use inklabs\kommerce\Action\Attribute\GetAttributeValueQuery;
use inklabs\kommerce\Action\Cart\CopyCartItemsCommand;
use inklabs\kommerce\Action\Cart\CreateCartCommand;
use inklabs\kommerce\Action\Cart\GetCartBySessionIdQuery;
use inklabs\kommerce\Action\Cart\GetCartByUserIdQuery;
use inklabs\kommerce\Action\Cart\GetCartQuery;
use inklabs\kommerce\Action\Cart\RemoveCartCommand;
use inklabs\kommerce\Action\Cart\SetCartSessionIdCommand;
use inklabs\kommerce\Action\Cart\SetCartUserCommand;
use inklabs\kommerce\Action\CartPriceRule\GetCartPriceRuleQuery;
use inklabs\kommerce\Action\CatalogPromotion\GetCatalogPromotionQuery;
use inklabs\kommerce\Action\Configuration\GetConfigurationsByKeysQuery;
use inklabs\kommerce\Action\Coupon\GetCouponQuery;
use inklabs\kommerce\Action\Option\GetOptionQuery;
use inklabs\kommerce\Action\Order\GetOrderItemQuery;
use inklabs\kommerce\Action\Order\GetOrderQuery;
use inklabs\kommerce\Action\Product\GetProductQuery;
use inklabs\kommerce\Action\Product\GetRandomProductsQuery;
use inklabs\kommerce\Action\Product\GetRelatedProductsQuery;
use inklabs\kommerce\Action\Shipment\GetShipmentTrackerQuery;
use inklabs\kommerce\Action\Tag\GetTagQuery;
use inklabs\kommerce\Action\Warehouse\GetInventoryLocationQuery;
use inklabs\kommerce\Action\Warehouse\GetWarehouseQuery;
use inklabs\kommerce\ActionResponse\Attribute\GetAttributeResponse;
use inklabs\kommerce\ActionResponse\Attribute\GetAttributeValueResponse;
use inklabs\kommerce\ActionResponse\Cart\GetCartBySessionIdResponse;
use inklabs\kommerce\ActionResponse\Cart\GetCartByUserIdResponse;
use inklabs\kommerce\ActionResponse\Cart\GetCartResponse;
use inklabs\kommerce\ActionResponse\CartPriceRule\GetCartPriceRuleResponse;
use inklabs\kommerce\ActionResponse\CatalogPromotion\GetCatalogPromotionResponse;
use inklabs\kommerce\ActionResponse\Configuration\GetConfigurationsByKeysResponse;
use inklabs\kommerce\ActionResponse\Coupon\GetCouponResponse;
use inklabs\kommerce\ActionResponse\Option\GetOptionResponse;
use inklabs\kommerce\ActionResponse\Order\GetOrderItemResponse;
use inklabs\kommerce\ActionResponse\Order\GetOrderResponse;
use inklabs\kommerce\ActionResponse\Product\GetProductResponse;
use inklabs\kommerce\ActionResponse\Product\GetRandomProductsResponse;
use inklabs\kommerce\ActionResponse\Product\GetRelatedProductsResponse;
use inklabs\kommerce\ActionResponse\Shipment\GetShipmentTrackerResponse;
use inklabs\kommerce\ActionResponse\Tag\GetTagResponse;
use inklabs\kommerce\ActionResponse\Warehouse\GetInventoryLocationResponse;
use inklabs\kommerce\ActionResponse\Warehouse\GetWarehouseResponse;
use inklabs\kommerce\EntityDTO\AttributeDTO;
use inklabs\kommerce\EntityDTO\AttributeValueDTO;
use inklabs\kommerce\EntityDTO\Builder\DTOBuilderFactoryInterface;
use inklabs\kommerce\EntityDTO\CartDTO;
use inklabs\kommerce\EntityDTO\CartPriceRuleDTO;
use inklabs\kommerce\EntityDTO\CatalogPromotionDTO;
use inklabs\kommerce\EntityDTO\ConfigurationDTO;
use inklabs\kommerce\EntityDTO\CouponDTO;
use inklabs\kommerce\EntityDTO\InventoryLocationDTO;
use inklabs\kommerce\EntityDTO\OptionDTO;
use inklabs\kommerce\EntityDTO\OrderAddressDTO;
use inklabs\kommerce\EntityDTO\OrderDTO;
use inklabs\kommerce\EntityDTO\OrderItemDTO;
use inklabs\kommerce\EntityDTO\PaginationDTO;
use inklabs\kommerce\EntityDTO\ProductDTO;
use inklabs\kommerce\EntityDTO\ShipmentTrackerDTO;
use inklabs\kommerce\EntityDTO\TagDTO;
use inklabs\kommerce\EntityDTO\UserDTO;
use inklabs\kommerce\EntityDTO\WarehouseDTO;
use inklabs\kommerce\Exception\EntityNotFoundException;
use inklabs\kommerce\Exception\InvalidArgumentException;
use inklabs\kommerce\Exception\KommerceException;
use inklabs\kommerce\Lib\CartCalculatorInterface;
use inklabs\kommerce\Lib\Command\CommandInterface;
use inklabs\kommerce\Lib\PricingInterface;
use inklabs\kommerce\Lib\Query\QueryInterface;
use inklabs\kommerce\Lib\Query\ResponseInterface;
use inklabs\kommerce\Lib\UuidInterface;
use inklabs\KommerceTemplates\Lib\AssetLocationService;
use inklabs\KommerceTemplates\Lib\TwigTemplate;
use inklabs\KommerceTemplates\Lib\TwigThemeConfig;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    /** @var CartDTO */
    private $cartDTO;

    /** @var UserDTO */
    private $userDTO;

    /** @var KommerceConfiguration */
    private $kommerceConfiguration;

    /** @var KommerceConfiguration */
    private $adminKommerceConfiguration;

    public function __construct()
    {
        $sessionId = $this->getSessionId();
        $isAdmin = false;
        $userId = null;
        $user = $this->getUserFromSession();
        if ($user !== null) {
            $userId = $user->id;
            $isAdmin = $this->userHasAdminRole($user);
            $twig = $this->getTwigTemplate();
            $twig->addGlobal('user', $user);
        }

        $this->kommerceConfiguration = new KommerceConfiguration(
            $userId,
            $sessionId,
            $isAdmin
        );

        $this->adminKommerceConfiguration = new KommerceConfiguration(
            null,
            null,
            true
        );
    }

    public function userHasAdminRole(UserDTO $user): bool
    {
        foreach ($user->userRoles as $userRole) {
            if ($userRole->userRoleType->isAdmin) {
                return true;
            }
        }

        return false;
    }

    public function getCaptchaBuilder(): CaptchaBuilder
    {
        $builder = new CaptchaBuilder();
        $builder->setBackgroundColor(255, 255, 255);
        $builder->setIgnoreAllEffects(true);
        $builder->build();

        $this->setCaptchaPhraseToSession($builder->getPhrase());

        return $builder;
    }

    private function setCaptchaPhraseToSession(string $phrase): void
    {
        // todo Fix: This is an awful way to set session variables
        /** @var \Illuminate\Session\Store $session */
        $session = app('session');
        $session->set('captchaPhrase', $phrase);
    }

    protected function getCaptchaPhraseFromSession(): string
    {
        // todo Fix: This is an awful way to set session variables
        /** @var \Illuminate\Session\Store $session */
        $session = app('session');
        $phrase = $session->get('captchaPhrase');
        $session->forget('captchaPhrase');
        return $phrase;
    }

    protected function saveUserToSession(UserDTO $user): void
    {
        /** @var \Illuminate\Session\Store $session */
        $session = app('session');
        $session->set('user', $user);
        $this->kommerceConfiguration->setUserId($user->id);
    }

    protected function removeUserFromSession(): void
    {
        /** @var \Illuminate\Session\Store $session */
        $session = app('session');

        if (! $session->has('user')) {
            abort(401);
        }

        $session->remove('user');
    }

    protected function getUserFromSessionOrAbort(): UserDTO
    {
        $user = $this->getUserFromSession();

        if ($user === null) {
            abort(401);
        }

        return $user;
    }

    protected function getUserFromSession(): ?UserDTO
    {
        /** @var \Illuminate\Session\Store $session */
        $session = app('session');

        if (! $session->has('user')) {
            return null;
        }

        return $session->get('user');
    }

    protected function getPricing(): PricingInterface
    {
        return $this->kommerceConfiguration->getPricing();
    }

    protected function getCartCalculator(): CartCalculatorInterface
    {
        return $this->kommerceConfiguration->getCartCalculator();
    }

    public function getDTOBuilderFactory(): DTOBuilderFactoryInterface
    {
        return $this->kommerceConfiguration->getDTOBuilderFactory();
    }

    protected function getStoreAddress(): OrderAddressDTO
    {
        return $this->kommerceConfiguration->getStoreAddress();
    }

    protected function dispatch(CommandInterface $command): void
    {
        $this->kommerceConfiguration->dispatch($command);
    }

    protected function dispatchQuery(QueryInterface $query)
    {
        return $this->kommerceConfiguration->dispatchQuery($query);
    }

    protected function adminDispatch(CommandInterface $command): void
    {
        $this->adminKommerceConfiguration->dispatch($command);
    }

    protected function adminDispatchQuery(QueryInterface $query): ResponseInterface
    {
        return $this->adminKommerceConfiguration->dispatchQuery($query);
    }

    protected function getRemoteIP4(): string
    {
        $remoteIp = request()->ip();

        if ($this->isIPv6($remoteIp)) {
            $remoteIp = "127.0.0.1";
        }

        return $remoteIp;
    }

    private function isIPv6(string $remoteIp): bool
    {
        return strpos($remoteIp, ':') !== false;
    }

    protected function getUserAgent(): string
    {
        return request()->header('User-Agent');
    }

    private function getSessionId(): string
    {
        return session()->getId();
    }

    /**
     * @return \Illuminate\Session\Store|\Illuminate\Session\SessionManager
     */
    private function getSession()
    {
        return session();
    }

    protected function getCart(): CartDTO
    {
        /** @var GetCartResponse $response */
        $response = $this->dispatchQuery(new GetCartQuery(
            $this->getCartId()
        ));
        return $response->getCartDTOWithAllData();
    }

    protected function getCartId(): string
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
     * @throws EntityNotFoundException
     */
    private function getCartFromSession(): CartDTO
    {
        /** @var GetCartBySessionIdResponse $response */
        $response = $this->adminDispatchQuery(new GetCartBySessionIdQuery($this->getSessionId()));
        return $response->getCartDTO();
    }

    private function getCartByUserId(string $userId): CartDTO
    {
        /** @var GetCartByUserIdResponse $response */
        $response = $this->adminDispatchQuery(new GetCartByUserIdQuery($userId));
        return $response->getCartDTO();
    }

    protected function createNewCart(): void
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

        /** @var GetCartResponse $response */
        $response = $this->dispatchQuery(new GetCartQuery($cartId));

        $this->cartDTO = $response->getCartDTO();
    }

    /**
     * @param string $name
     * @param array $context
     * @return \Illuminate\Contracts\Routing\ResponseFactory | \Symfony\Component\HttpFoundation\Response
     */
    protected function renderTemplate(string $name, array $context = [])
    {
        $twig = $this->getTwigTemplate();

        $this->setGlobalFlashVariables($twig);

        return response(
            $twig->render($name, $context)
        );
    }

    private function setGlobalFlashVariables(TwigTemplate $twig): void
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

    protected function getTwigTemplate(): TwigTemplate
    {
        static $twigTemplate = null;
        if ($twigTemplate === null) {
            $twigTemplate = new TwigTemplate(
                TwigThemeConfig::loadConfigFromTheme(env('STORE_THEME'), 'store'),
                TwigThemeConfig::loadConfigFromTheme(env('ADMIN_THEME'), 'admin'),
                new CSRFTokenGenerator(),
                new LaravelRouteUrl(),
                env('STORE_TIMEZONE'),
                env('STORE_DATE_FORMAT'),
                env('STORE_TIME_FORMAT'),
                env('TWIG_PROFILER_ENABLED'),
                env('TWIG_DEBUG_ENABLED')
            );
        }

        return $twigTemplate;
    }

    protected function flashSuccess(string $message = ''): void
    {
        $this->flashMessage('success', $message);
    }

    protected function flashTemplateError(string $flashTemplate, array $data = []): void
    {
//        if (strpos($flashTemplate, '@store/flash/') === false) {
//            throw new InvalidArgumentException('Can only flash from @store/flash/');
//        }
        $this->flashTemplateMessage('error', $flashTemplate, $data);
    }

    /**
     * @param string $flashTemplate
     * @param array $data
     * @throws InvalidArgumentException
     */
    protected function flashTemplateSuccess(string $flashTemplate, array $data = []): void
    {
        if (strpos($flashTemplate, '@store/flash/') === false) {
            throw new InvalidArgumentException('Can only flash from @store/flash/');
        }
        $this->flashTemplateMessage('success', $flashTemplate, $data);
    }

    protected function flashError(string $message = ''): void
    {
        $this->flashMessage('danger', $message);
    }

    protected function flashWarning(string $message = ''): void
    {
        $this->flashMessage('warning', $message);
    }

    public function flashGenericWarning(string $message = 'Something went wrong.'): void
    {
        $extraMessage = 'Please contact us at store@example.com';
        $this->flashWarning($message . ' ' . $extraMessage);
    }

    private function flashMessage(string $type, string $message = ''): void
    {
        $messages = session()->get('flashMessages', []);
        $messages[$type][] = $message;
        session()->flash('flashMessages', $messages);
    }

    protected function flashFormErrors(ConstraintViolationListInterface $newFormErrors): void
    {
        /** @var ConstraintViolationListInterface | ConstraintViolationInterface[] $formErrors */
        $formErrors = session()->get('flashFormErrors', new ConstraintViolationList());
        $formErrors->addAll($newFormErrors);
        session()->flash('flashFormErrors', $formErrors);
    }

    private function flashTemplateMessage(string $type, string $flashTemplate, array $data): void
    {
        $messages = session()->get('templateFlashMessages', []);
        $messages[$type][] = [
            'flashTemplate' => $flashTemplate,
            'data' => $data,
        ];
        session()->flash('templateFlashMessages', $messages);
    }

    protected function getPaginationDTO(int $maxResults): PaginationDTO
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

    protected function getAssetLocationService(): AssetLocationService
    {
        $assetLocationService = new AssetLocationService();
        return $assetLocationService;
    }

    /**
     * @param int $limit
     * @return ProductDTO[]
     */
    protected function getRandomProducts(int $limit): array
    {
        /** @var GetRandomProductsResponse $response */
        $response = $this->dispatchQuery(new GetRandomProductsQuery($limit));
        return $response->getProductDTOs();
    }

    /**
     * @param array $keys
     * @return ConfigurationDTO[]
     */
    protected function getConfigurationsByKeys(array $keys): array
    {
        /** @var GetConfigurationsByKeysResponse $response */
        $response = $this->dispatchQuery(new GetConfigurationsByKeysQuery($keys));
        return $response->getConfigurationDTOs();
    }

    protected function getOptionWithAllData(string $optionId): OptionDTO
    {
        return $this->getOptionById($optionId)
            ->getOptionDTOWithAllData();
    }

    private function getOptionById(string $optionId): GetOptionResponse
    {
        try {
            return $this->dispatchQuery(new GetOptionQuery($optionId));
        } catch (EntityNotFoundException $e) {
            return abort(404);
        }
    }

    protected function getOrderWithAllData(string $orderId): OrderDTO
    {
        return $this->getOrderById($orderId)
            ->getOrderDTOWithAllData();
    }

    private function getOrderById(string $orderId): GetOrderResponse
    {
        try {
            return $this->dispatchQuery(new GetOrderQuery($orderId));
        } catch (EntityNotFoundException $e) {
            return abort(404);
        }
    }

    protected function getShipmentTracker(string $shipmentTrackerId): ShipmentTrackerDTO
    {
        return $this->getShipmentTrackerById($shipmentTrackerId)
            ->getShipmentTrackerDTO();
    }

    private function getShipmentTrackerById(string $shipmentTrackerId): GetShipmentTrackerResponse
    {
        try {
            return $this->dispatchQuery(new GetShipmentTrackerQuery($shipmentTrackerId));
        } catch (EntityNotFoundException $e) {
            return abort(404);
        }
    }

    protected function getOrderItem(string $orderItemId): OrderItemDTO
    {
        return $this->getOrderItemById($orderItemId)
            ->getOrderItemDTO();
    }

    protected function getOrderItemWithAllData(string $orderItemId): OrderItemDTO
    {
        return $this->getOrderItemById($orderItemId)
            ->getOrderItemDTOWithAllData();
    }

    private function getOrderItemById(string $orderItemId): GetOrderItemResponse
    {
        try {
            return $this->dispatchQuery(new GetOrderItemQuery($orderItemId));
        } catch (EntityNotFoundException $e) {
            return abort(404);
        }
    }

    protected function getProduct(string $productId): ProductDTO
    {
        return $this->getProductById($productId)
            ->getProductDTO();
    }

    protected function getProductWithAllData(string $productId): ProductDTO
    {
        return $this->getProductById($productId)
            ->getProductDTOWithAllData();
    }

    private function getProductById(string $productId): GetProductResponse
    {
        try {
            return $this->dispatchQuery(new GetProductQuery($productId));
        } catch (EntityNotFoundException $e) {
            return abort(404);
        }
    }

    protected function getCartPriceRule(string $cartPriceRuleId): CartPriceRuleDTO
    {
        return $this->getCartPriceRuleById($cartPriceRuleId)
            ->getCartPriceRuleDTO();
    }

    protected function getCartPriceRuleWithAllData(string $cartPriceRuleId): CartPriceRuleDTO
    {
        return $this->getCartPriceRuleById($cartPriceRuleId)
            ->getCartPriceRuleDTOWithAllData();
    }

    private function getCartPriceRuleById(string $cartPriceRuleId): GetCartPriceRuleResponse
    {
        try {
            return $this->dispatchQuery(new GetCartPriceRuleQuery($cartPriceRuleId));
        } catch (EntityNotFoundException $e) {
            return abort(404);
        }
    }

    protected function getAttributeWithAllData(string $attributeId): AttributeDTO
    {
        return $this->getAttributeById($attributeId)
            ->getAttributeDTOWithAttributeValues();
    }

    protected function getAttribute(string $attributeId): AttributeDTO
    {
        return $this->getAttributeById($attributeId)
            ->getAttributeDTO();
    }

    private function getAttributeById(string $attributeId): GetAttributeResponse
    {
        try {
            return $this->dispatchQuery(new GetAttributeQuery($attributeId));
        } catch (EntityNotFoundException $e) {
            return abort(404);
        }
    }

    protected function getAttributeValueWithAllData(string $attributeValueId): AttributeValueDTO
    {
        return $this->getAttributeValueById($attributeValueId)
            ->getAttributeValueDTOWithAllData();
    }

    protected function getAttributeValue(string $attributeValueId): AttributeValueDTO
    {
        return $this->getAttributeValueById($attributeValueId)
            ->getAttributeValueDTO();
    }

    private function getAttributeValueById(string $attributeValueId): GetAttributeValueResponse
    {
        try {
            return $this->dispatchQuery(new GetAttributeValueQuery($attributeValueId));
        } catch (EntityNotFoundException $e) {
            return abort(404);
        }
    }

    protected function getCoupon(string $couponId): CouponDTO
    {
        return $this->getCouponById($couponId)
            ->getCouponDTO();
    }

    private function getCouponById(string $couponId): GetCouponResponse
    {
        try {
            return $this->dispatchQuery(new GetCouponQuery($couponId));
        } catch (EntityNotFoundException $e) {
            return abort(404);
        }
    }

    protected function getWarehouseWithAllData(string $warehouseId): WarehouseDTO
    {
        return $this->getWarehouseById($warehouseId)
            ->getWarehouseDTOWithAllData();
    }

    protected function getWarehouse(string $warehouseId): WarehouseDTO
    {
        return $this->getWarehouseById($warehouseId)
            ->getWarehouseDTO();
    }

    private function getWarehouseById(string $warehouseId): GetWarehouseResponse
    {
        try {
            return $this->dispatchQuery(new GetWarehouseQuery($warehouseId));
        } catch (EntityNotFoundException $e) {
            return abort(404);
        }
    }

    protected function getInventoryLocationWithAllData(string $inventoryLocationId): InventoryLocationDTO
    {
        return $this->getInventoryLocationById($inventoryLocationId)
            ->getInventoryLocationDTOWithAllData();
    }

    protected function getInventoryLocation(string $inventoryLocationId): InventoryLocationDTO
    {
        return $this->getInventoryLocationById($inventoryLocationId)
            ->getInventoryLocationDTO();
    }

    private function getInventoryLocationById(string $inventoryLocationId): GetInventoryLocationResponse
    {
        try {
            return $this->dispatchQuery(new GetInventoryLocationQuery($inventoryLocationId));
        } catch (EntityNotFoundException $e) {
            return abort(404);
        }
    }

    protected function getCatalogPromotionWithAllData(string $catalogPromotionId): CatalogPromotionDTO
    {
        return $this->getCatalogPromotionById($catalogPromotionId)
            ->getCatalogPromotionDTOWithAllData();
    }

    protected function getCatalogPromotion(string $catalogPromotionId): CatalogPromotionDTO
    {
        return $this->getCatalogPromotionById($catalogPromotionId)
            ->getCatalogPromotionDTO();
    }

    private function getCatalogPromotionById(string $catalogPromotionId): GetCatalogPromotionResponse
    {
        try {
            return $this->dispatchQuery(new GetCatalogPromotionQuery($catalogPromotionId));
        } catch (EntityNotFoundException $e) {
            return abort(404);
        }
    }

    protected function getTag(string $tagId): TagDTO
    {
        return $this->getTagById($tagId)
            ->getTagDTO();
    }

    protected function getTagWithAllData(string $tagId): TagDTO
    {
        return $this->getTagById($tagId)
            ->getTagDTOWithAllData();
    }

    private function getTagById(string $tagId): GetTagResponse
    {
        try {
            return $this->dispatchQuery(new GetTagQuery($tagId));
        } catch (EntityNotFoundException $e) {
            return abort(404);
        }
    }

    protected function serveFile(string $filePath)
    {
        if (!file_exists($filePath)) {
            abort(404);
        }

        header('Content-Length: ' . filesize($filePath));
        header('Content-type: ' . $this->getMimeType($filePath));
        header('Expires: ' . date('r', strtotime('now +1 week')));
        header('Last-Modified: ' . date('r', filemtime($filePath)));
        header('Cache-Control: max-age=604800');
        ob_clean();
        flush();

        readfile($filePath);
        exit;
    }

    protected function getMimeType(string $file): string
    {
        $mime_types = [
            "pdf" => "application/pdf",
            "zip" => "application/zip",
            "docx" => "application/msword",
            "doc" => "application/msword",
            "xls" => "application/vnd.ms-excel",
            "ppt" => "application/vnd.ms-powerpoint",
            "gif" => "image/gif",
            "png" => "image/png",
            "jpeg" => "image/jpg",
            "jpg" => "image/jpg",
            "mp3" => "audio/mpeg",
            "wav" => "audio/x-wav",
            "mpeg" => "video/mpeg",
            "mpg" => "video/mpeg",
            "mpe" => "video/mpeg",
            "mov" => "video/quicktime",
            "avi" => "video/x-msvideo",
            "3gp" => "video/3gpp",
            "css" => "text/css",
            "jsc" => "application/javascript",
            "js" => "application/javascript",
            "php" => "text/html",
            "htm" => "text/html",
            "html" => "text/html",
        ];

        $variable = explode('.', $file);

        $extension = strtolower(end($variable));

        return $mime_types[$extension];
    }

    protected function getCentsOrNull(string $value): ?int
    {
        if ($value === '') {
            return null;
        }

        return (int) ($value * 100);
    }

    protected function getIntOrNull(string $value): ?int
    {
        if ($value === '') {
            return null;
        }

        return (int) $value;
    }

    protected function getFloatOrNull(string $value): ?float
    {
        if ($value === '') {
            return null;
        }

        return (float) $value;
    }

    protected function getStringOrNull(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        return (string) $value;
    }

    protected function getTimestampFromDateTimeTimezoneInput(array $dateTime): ?int
    {
        $date = Arr::get($dateTime, 'date');
        $time = Arr::get($dateTime, 'time');
        $timezone = Arr::get($dateTime, 'timezone');

        if (trim($date . $time) !== '') {
            $startDateTime = new DateTime($date . ' ' . $time, new DateTimeZone($timezone));
            return $startDateTime->getTimestamp();
        }

        return null;
    }

    protected function mergeCart(UuidInterface $userId): void
    {
        $sessionId = $this->getSessionId();
        $userCart = null;
        $sessionCart = null;

        try {
            $userCart = $this->getCartByUserId($userId->getHex());
        } catch (EntityNotFoundException $e) {
        } catch (KommerceException $e) {
            $this->logError($e->getMessage());
        }

        try {
            $sessionCart = $this->getCartFromSession();
        } catch (EntityNotFoundException $e) {
        } catch (KommerceException $e) {
            $this->logError($e->getMessage());
        }

        try {
            if ($userCart === null && $sessionCart !== null) {
                // SessionCart Exists
                $this->dispatch(new SetCartUserCommand(
                    $sessionCart->id->getHex(),
                    $userId->getHex()
                ));
            } elseif ($userCart !== null && $sessionCart === null) {
                // UserCart Exists
                $this->dispatch(new SetCartSessionIdCommand(
                    $userCart->id->getHex(),
                    $sessionId
                ));
            } elseif ($userCart !== null && $sessionCart !== null) {
                if ($userCart->id->equals($sessionCart->id)) {
                    return;
                }

                // Both Exist
                $this->dispatch(new CopyCartItemsCommand(
                    $sessionCart->id->getHex(),
                    $userCart->id->getHex()
                ));
                $this->dispatch(new RemoveCartCommand($sessionCart->id->getHex()));
                $this->dispatch(new SetCartSessionIdCommand(
                    $userCart->id->getHex(),
                    $sessionId
                ));
            }
        } catch (KommerceException $e) {
            $this->logError($e->getMessage());
            $this->flashGenericWarning('Unable to merge your existing cart.');
        }
    }

    private function logError(string $message)
    {
        // TODO: Log error message to file
    }

    /**
     * @param CartDTO $cartDTO
     * @param int $limit
     * @return ProductDTO[]
     */
    protected function getRelatedProducts(CartDTO $cartDTO, int $limit = 4): array
    {
        $cartProductIds = [];
        foreach ($cartDTO->cartItems as $cartItem) {
            $cartProductIds[] = $cartItem->product->id->getHex();
        }

        return $this->getRecommendedProducts($cartProductIds, $limit);
    }

    /**
     * @param string[] $productIds
     * @param int $limit
     * @return ProductDTO[]
     */
    protected function getRecommendedProducts(array $productIds, int $limit): array
    {
        /** @var GetRelatedProductsResponse $response */
        $response = $this->dispatchQuery(new GetRelatedProductsQuery($productIds, $limit));
        return $response->getProductDTOs();
    }
}
