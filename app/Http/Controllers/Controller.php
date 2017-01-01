<?php
namespace App\Http\Controllers;

use App\Lib\Arr;
use App\Lib\CSRFTokenGenerator;
use App\Lib\KommerceConfiguration;
use App\Lib\LaravelRouteUrl;
use DateTime;
use DateTimeZone;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use inklabs\kommerce\Action\Attribute\GetAttributeQuery;
use inklabs\kommerce\Action\Attribute\GetAttributeValueQuery;
use inklabs\kommerce\Action\Attribute\Query\GetAttributeRequest;
use inklabs\kommerce\Action\Attribute\Query\GetAttributeResponse;
use inklabs\kommerce\Action\Attribute\Query\GetAttributeValueRequest;
use inklabs\kommerce\Action\Attribute\Query\GetAttributeValueResponse;
use inklabs\kommerce\Action\Cart\CreateCartCommand;
use inklabs\kommerce\Action\Cart\GetCartBySessionIdQuery;
use inklabs\kommerce\Action\Cart\GetCartQuery;
use inklabs\kommerce\Action\Cart\Query\GetCartBySessionIdRequest;
use inklabs\kommerce\Action\Cart\Query\GetCartBySessionIdResponse;
use inklabs\kommerce\Action\Cart\Query\GetCartRequest;
use inklabs\kommerce\Action\Cart\Query\GetCartResponse;
use inklabs\kommerce\Action\CartPriceRule\GetCartPriceRuleQuery;
use inklabs\kommerce\Action\CartPriceRule\Query\GetCartPriceRuleRequest;
use inklabs\kommerce\Action\CartPriceRule\Query\GetCartPriceRuleResponse;
use inklabs\kommerce\Action\CatalogPromotion\GetCatalogPromotionQuery;
use inklabs\kommerce\Action\CatalogPromotion\Query\GetCatalogPromotionRequest;
use inklabs\kommerce\Action\CatalogPromotion\Query\GetCatalogPromotionResponse;
use inklabs\kommerce\Action\Coupon\GetCouponQuery;
use inklabs\kommerce\Action\Coupon\Query\GetCouponRequest;
use inklabs\kommerce\Action\Coupon\Query\GetCouponResponse;
use inklabs\kommerce\Action\Option\GetOptionQuery;
use inklabs\kommerce\Action\Option\Query\GetOptionRequest;
use inklabs\kommerce\Action\Option\Query\GetOptionResponse;
use inklabs\kommerce\Action\Order\GetOrderItemQuery;
use inklabs\kommerce\Action\Order\GetOrderQuery;
use inklabs\kommerce\Action\Order\Query\GetOrderItemRequest;
use inklabs\kommerce\Action\Order\Query\GetOrderItemResponse;
use inklabs\kommerce\Action\Order\Query\GetOrderRequest;
use inklabs\kommerce\Action\Order\Query\GetOrderResponse;
use inklabs\kommerce\Action\Product\GetProductQuery;
use inklabs\kommerce\Action\Product\GetRandomProductsQuery;
use inklabs\kommerce\Action\Product\Query\GetProductRequest;
use inklabs\kommerce\Action\Product\Query\GetProductResponse;
use inklabs\kommerce\Action\Product\Query\GetRandomProductsRequest;
use inklabs\kommerce\Action\Product\Query\GetRandomProductsResponse;
use inklabs\kommerce\Action\Tag\GetTagQuery;
use inklabs\kommerce\Action\Tag\Query\GetTagRequest;
use inklabs\kommerce\Action\Tag\Query\GetTagResponse;
use inklabs\kommerce\EntityDTO\AttributeDTO;
use inklabs\kommerce\EntityDTO\AttributeValueDTO;
use inklabs\kommerce\EntityDTO\CartDTO;
use inklabs\kommerce\EntityDTO\CartPriceRuleDTO;
use inklabs\kommerce\EntityDTO\CatalogPromotionDTO;
use inklabs\kommerce\EntityDTO\CouponDTO;
use inklabs\kommerce\EntityDTO\OptionDTO;
use inklabs\kommerce\EntityDTO\OrderDTO;
use inklabs\kommerce\EntityDTO\OrderItemDTO;
use inklabs\kommerce\EntityDTO\PaginationDTO;
use inklabs\kommerce\EntityDTO\ProductDTO;
use inklabs\kommerce\EntityDTO\TagDTO;
use inklabs\kommerce\EntityDTO\UserDTO;
use inklabs\kommerce\Exception\EntityNotFoundException;
use inklabs\kommerce\Exception\InvalidArgumentException;
use inklabs\kommerce\Lib\Command\CommandInterface;
use inklabs\kommerce\Lib\Query\QueryInterface;
use inklabs\KommerceTemplates\Lib\AssetLocationService;
use inklabs\KommerceTemplates\Lib\TwigTemplate;
use inklabs\KommerceTemplates\Lib\TwigThemeConfig;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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

    public function __construct()
    {
        $this->kommerceConfiguration = new KommerceConfiguration();
    }

    protected function getPricing()
    {
        return $this->kommerceConfiguration->getPricing();
    }

    protected function getCartCalculator()
    {
        return $this->kommerceConfiguration->getCartCalculator();
    }

    public function getDTOBuilderFactory()
    {
        return $this->kommerceConfiguration->getDTOBuilderFactory();
    }

    protected function dispatch(CommandInterface $command)
    {
        $this->kommerceConfiguration->dispatch($command);
    }

    protected function dispatchQuery(QueryInterface $query)
    {
        $this->kommerceConfiguration->dispatchQuery($query);
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
        $response = new GetCartBySessionIdResponse($this->getCartCalculator());
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
            env('STORE_TIME_FORMAT'),
            env('TWIG_PROFILER_ENABLED'),
            env('TWIG_DEBUG_ENABLED')
        );

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
     * @throws InvalidArgumentException
     */
    protected function flashTemplateSuccess($flashTemplate, array $data)
    {
        if (strpos($flashTemplate, '@theme/flash/') === false) {
            throw new InvalidArgumentException('Can only flash from @theme/flash/');
        }
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

    /**
     * @param $optionId
     * @return OptionDTO
     * @throws NotFoundHttpException
     */
    protected function getOptionWithAllData($optionId)
    {
        return $this->getOptionById($optionId)
            ->getOptionDTOWithAllData();
    }

    /**
     * @param string $optionId
     * @return GetOptionResponse
     * @throws NotFoundHttpException
     */
    private function getOptionById($optionId)
    {
        try {
            $request = new GetOptionRequest($optionId);
            $response = new GetOptionResponse($this->getPricing());
            $this->dispatchQuery(new GetOptionQuery($request, $response));
        } catch (EntityNotFoundException $e) {
            return abort(404);
        }

        return $response;
    }

    /**
     * @param $orderId
     * @return OrderDTO
     * @throws NotFoundHttpException
     */
    protected function getOrderWithAllData($orderId)
    {
        return $this->getOrderById($orderId)
            ->getOrderDTOWithAllData();
    }

    /**
     * @param string $orderId
     * @return GetOrderResponse
     * @throws NotFoundHttpException
     */
    private function getOrderById($orderId)
    {
        try {
            $request = new GetOrderRequest($orderId);
            $response = new GetOrderResponse();
            $this->dispatchQuery(new GetOrderQuery($request, $response));
        } catch (EntityNotFoundException $e) {
            return abort(404);
        }

        return $response;
    }

    /**
     * @param $orderItemId
     * @return OrderItemDTO
     * @throws NotFoundHttpException
     */
    protected function getOrderItem($orderItemId)
    {
        return $this->getOrderItemById($orderItemId)
            ->getOrderItemDTO();
    }

    /**
     * @param $orderItemId
     * @return OrderItemDTO
     * @throws NotFoundHttpException
     */
    protected function getOrderItemWithAllData($orderItemId)
    {
        return $this->getOrderItemById($orderItemId)
            ->getOrderItemDTOWithAllData();
    }

    /**
     * @param string $orderItemId
     * @return GetOrderItemResponse
     * @throws NotFoundHttpException
     */
    private function getOrderItemById($orderItemId)
    {
        try {
            $request = new GetOrderItemRequest($orderItemId);
            $response = new GetOrderItemResponse();
            $this->dispatchQuery(new GetOrderItemQuery($request, $response));
        } catch (EntityNotFoundException $e) {
            return abort(404);
        }

        return $response;
    }

    /**
     * @param $productId
     * @return ProductDTO
     * @throws NotFoundHttpException
     */
    protected function getProduct($productId)
    {
        return $this->getProductById($productId)
            ->getProductDTO();
    }

    /**
     * @param $productId
     * @return ProductDTO
     * @throws NotFoundHttpException
     */
    protected function getProductWithAllData($productId)
    {
        return $this->getProductById($productId)
            ->getProductDTOWithAllData();
    }

    /**
     * @param string $productId
     * @return GetProductResponse
     * @throws NotFoundHttpException
     */
    private function getProductById($productId)
    {
        try {
            $request = new GetProductRequest($productId);
            $response = new GetProductResponse($this->getPricing());
            $this->dispatchQuery(new GetProductQuery($request, $response));
        } catch (EntityNotFoundException $e) {
            return abort(404);
        }

        return $response;
    }

    /**
     * @param $cartPriceRuleId
     * @return CartPriceRuleDTO
     * @throws NotFoundHttpException
     */
    protected function getCartPriceRule($cartPriceRuleId)
    {
        return $this->getCartPriceRuleById($cartPriceRuleId)
            ->getCartPriceRuleDTO();
    }

    /**
     * @param $cartPriceRuleId
     * @return CartPriceRuleDTO
     * @throws NotFoundHttpException
     */
    protected function getCartPriceRuleWithAllData($cartPriceRuleId)
    {
        return $this->getCartPriceRuleById($cartPriceRuleId)
            ->getCartPriceRuleDTOWithAllData();
    }

    /**
     * @param string $cartPriceRuleId
     * @return GetCartPriceRuleResponse
     * @throws NotFoundHttpException
     */
    private function getCartPriceRuleById($cartPriceRuleId)
    {
        try {
            $request = new GetCartPriceRuleRequest($cartPriceRuleId);
            $response = new GetCartPriceRuleResponse();
            $this->dispatchQuery(new GetCartPriceRuleQuery($request, $response));
        } catch (EntityNotFoundException $e) {
            return abort(404);
        }

        return $response;
    }

    /**
     * @param $attributeId
     * @return AttributeDTO
     * @throws NotFoundHttpException
     */
    protected function getAttributeWithAllData($attributeId)
    {
        return $this->getAttributeById($attributeId)
            ->getAttributeDTOWithAttributeValues();
    }

    /**
     * @param $attributeId
     * @return AttributeDTO
     * @throws NotFoundHttpException
     */
    protected function getAttribute($attributeId)
    {
        return $this->getAttributeById($attributeId)
            ->getAttributeDTO();
    }

    /**
     * @param string $attributeId
     * @return GetAttributeResponse
     * @throws NotFoundHttpException
     */
    private function getAttributeById($attributeId)
    {
        try {
            $request = new GetAttributeRequest($attributeId);
            $response = new GetAttributeResponse();
            $this->dispatchQuery(new GetAttributeQuery($request, $response));
        } catch (EntityNotFoundException $e) {
            return abort(404);
        }

        return $response;
    }

    /**
     * @param $attributeValueId
     * @return AttributeValueDTO
     * @throws NotFoundHttpException
     */
    protected function getAttributeValueWithAllData($attributeValueId)
    {
        return $this->getAttributeValueById($attributeValueId)
            ->getAttributeValueDTOWithAllData();
    }

    /**
     * @param $attributeValueId
     * @return AttributeValueDTO
     * @throws NotFoundHttpException
     */
    protected function getAttributeValue($attributeValueId)
    {
        return $this->getAttributeValueById($attributeValueId)
            ->getAttributeValueDTO();
    }

    /**
     * @param string $attributeValueId
     * @return GetAttributeValueResponse
     * @throws NotFoundHttpException
     */
    private function getAttributeValueById($attributeValueId)
    {
        try {
            $request = new GetAttributeValueRequest($attributeValueId);
            $response = new GetAttributeValueResponse();
            $this->dispatchQuery(new GetAttributeValueQuery($request, $response));
        } catch (EntityNotFoundException $e) {
            return abort(404);
        }

        return $response;
    }

    /**
     * @param $couponId
     * @return CouponDTO
     * @throws NotFoundHttpException
     */
    protected function getCoupon($couponId)
    {
        return $this->getCouponById($couponId)
            ->getCouponDTO();
    }

    /**
     * @param string $couponId
     * @return GetCouponResponse
     * @throws NotFoundHttpException
     */
    private function getCouponById($couponId)
    {
        try {
            $request = new GetCouponRequest($couponId);
            $response = new GetCouponResponse();
            $this->dispatchQuery(new GetCouponQuery($request, $response));
        } catch (EntityNotFoundException $e) {
            return abort(404);
        }

        return $response;
    }

    /**
     * @param $catalogPromotionId
     * @return CatalogPromotionDTO
     * @throws NotFoundHttpException
     */
    protected function getCatalogPromotionWithAllData($catalogPromotionId)
    {
        return $this->getCatalogPromotionById($catalogPromotionId)
            ->getCatalogPromotionDTOWithAllData();
    }

    /**
     * @param $catalogPromotionId
     * @return CatalogPromotionDTO
     * @throws NotFoundHttpException
     */
    protected function getCatalogPromotion($catalogPromotionId)
    {
        return $this->getCatalogPromotionById($catalogPromotionId)
            ->getCatalogPromotionDTO();
    }

    /**
     * @param string $catalogPromotionId
     * @return GetCatalogPromotionResponse
     * @throws NotFoundHttpException
     */
    private function getCatalogPromotionById($catalogPromotionId)
    {
        try {
            $request = new GetCatalogPromotionRequest($catalogPromotionId);
            $response = new GetCatalogPromotionResponse($this->getPricing());
            $this->dispatchQuery(new GetCatalogPromotionQuery($request, $response));
        } catch (EntityNotFoundException $e) {
            return abort(404);
        }

        return $response;
    }

    /**
     * @param $tagId
     * @return TagDTO
     * @throws NotFoundHttpException
     */
    protected function getTag($tagId)
    {
        return $this->getTagById($tagId)
            ->getTagDTO();
    }

    /**
     * @param $tagId
     * @return TagDTO
     * @throws NotFoundHttpException
     */
    protected function getTagWithAllData($tagId)
    {
        return $this->getTagById($tagId)
            ->getTagDTOWithAllData();
    }

    /**
     * @param string $tagId
     * @return GetTagResponse
     * @throws NotFoundHttpException
     */
    private function getTagById($tagId)
    {
        try {
            $request = new GetTagRequest($tagId);
            $response = new GetTagResponse($this->getPricing());
            $this->dispatchQuery(new GetTagQuery($request, $response));
        } catch (EntityNotFoundException $e) {
            return abort(404);
        }

        return $response;
    }

    /**
     * @param string $filePath
     */
    protected function serveFile($filePath)
    {
        if (!file_exists($filePath)) {
            abort(404);
        }

        header('Content-Length: ' . filesize($filePath));
        header('Content-Type: ' . mime_content_type($filePath));
        header('Expires: ' . date('r', strtotime('now +1 week')));
        header('Last-Modified: ' . date('r', filemtime($filePath)));
        header('Cache-Control: max-age=604800');
        ob_clean();
        flush();

        readfile($filePath);
        exit;
    }

    /**
     * @param string $value
     * @return null|int
     */
    protected function getCentsOrNull($value)
    {
        if ($value === '') {
            return null;
        }

        return (int) ($value * 100);
    }

    /**
     * @param string $value
     * @return null|int
     */
    protected function getIntOrNull($value)
    {
        if ($value === '') {
            return null;
        }

        return (int) $value;
    }

    /**
     * @param string $value
     * @return null|string
     */
    protected function getStringOrNull($value)
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        return (string) $value;
    }

    /**
     * @param array $dateTime
     * @return int|null
     */
    protected function getTimestampFromDateTimeTimezoneInput(array $dateTime)
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
}
