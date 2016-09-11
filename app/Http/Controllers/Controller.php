<?php
namespace App\Http\Controllers;

use App\Lib\CSRFTokenGenerator;
use App\Lib\KommerceConfiguration;
use App\Lib\LaravelRouteUrl;
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
use inklabs\kommerce\Action\Order\GetOrderQuery;
use inklabs\kommerce\Action\Order\Query\GetOrderRequest;
use inklabs\kommerce\Action\Order\Query\GetOrderResponse;
use inklabs\kommerce\Action\Product\GetRandomProductsQuery;
use inklabs\kommerce\Action\Product\Query\GetRandomProductsRequest;
use inklabs\kommerce\Action\Product\Query\GetRandomProductsResponse;
use inklabs\kommerce\EntityDTO\CartDTO;
use inklabs\kommerce\EntityDTO\OrderDTO;
use inklabs\kommerce\EntityDTO\PaginationDTO;
use inklabs\kommerce\EntityDTO\UserDTO;
use inklabs\kommerce\Exception\EntityNotFoundException;
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
}
