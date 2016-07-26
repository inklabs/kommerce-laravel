<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

use App\Http\Requests;
use inklabs\kommerce\Exception\KommerceException;
use ReflectionClass;

class ApiController extends Controller
{
    const HANDS_IMG = '<img src="http://i.imgur.com/bq4XXwm.png">';

    public function process(Request $request, $model, $action)
    {
        if ($this->actionIsQuery($action)) {
            return $this->processQuery($request, $model, $action);
        } elseif ($this->actionIsCommand($action)) {
            return $this->processCommand($request, $model, $action);
        } else {
            return self::HANDS_IMG;
        }
    }

    /**
     * @param string $requestClassName
     * @param array $query
     * @return mixed
     */
    private function getDynamicObject($requestClassName, $query)
    {
        $reflection = new ReflectionClass($requestClassName);

        $constructorParameters = [];

        foreach ($query as $key => $value) {
            $constructorParameters[] = $value;
        }

        if (! empty($constructorParameters)) {
            $requestObject = $reflection->newInstanceArgs($constructorParameters);
        } else {
            $requestObject = $reflection->newInstance();
        }

        return $requestObject;
    }

    private function actionIsQuery($action)
    {
        return stripos($action, 'Query') !== false;
    }

    private function actionIsCommand($action)
    {
        return stripos($action, 'Command') !== false;
    }

    private function processQuery(Request $request, $model, $action)
    {
        $query = $request->query();

        $queryActionName = $action;
        $requestActionName = str_replace('Query', 'Request', $queryActionName);
        $responseActionName = str_replace('Query', 'Response', $queryActionName);

        $queryClassName = '\\inklabs\\kommerce\\Action\\' . $model . '\\' . $queryActionName;
        $requestClassName = '\\inklabs\\kommerce\\Action\\' . $model . '\\Query\\' . $requestActionName;
        $responseClassName = '\\inklabs\\kommerce\\Action\\' . $model . '\\Query\\' . $responseActionName;

        $requestObject = $this->getDynamicObject($requestClassName, $query);

        $responseObject = new $responseClassName($this->getPricing());
        $queryObject = new $queryClassName($requestObject, $responseObject);

        $this->dispatchQuery($queryObject);

        if (method_exists($responseObject, 'getProductDTOWithAllData')) {
            return [$responseObject->getProductDTOWithAllData()];
        } elseif (method_exists($responseObject, 'getProductDTO')) {
            return [$responseObject->getProductDTO()];
        } elseif (method_exists($responseObject, 'getProductDTOs')) {
            return $responseObject->getProductDTOs();
        } else {
            return self::HANDS_IMG;
        }

        /**
         * Example:
         *  http://localhost:8000/api/v1/Product/GetRandomProductsQuery?limit=5
         *  http://localhost:8000/api/v1/Product/GetProductQuery?id=63FBF0B1875447D9B6ECD04BCB74B9A1
         */
    }

    private function processCommand(Request $request, $model, $action)
    {
        $post = $request->query();

        $commandClassName = '\\inklabs\\kommerce\\Action\\' . $model . '\\' . $action;

        $commandObject = $this->getDynamicObject($commandClassName, $post);

        try {
            $this->dispatch($commandObject);
        } catch (Exception $e) {
            return [
                'isSuccess' => false,
                'message' => $e->getMessage(),
                'data' => [],
            ];
        }

        $dataCollection = [];
        if (method_exists($commandObject, 'getProductId')) {
            $dataCollection['productId'] = $commandObject->getProductId();
        }

        if (method_exists($commandObject, 'getTagId')) {
            $dataCollection['tagId'] = $commandObject->getTagId();
        }

        return [
            'isSuccess' => true,
            'data' => $dataCollection
        ];

        /**
         * Example:
         *  http://localhost:8000/api/v1/Product/AddTagToProductCommand?productId=63FBF0B1875447D9B6ECD04BCB74B9A1&tagId=f77980b69e0b4eaabe6f8fd859e62a8e
         *  http://localhost:8000/api/v1/Product/RemoveTagFromProductCommand?productId=63FBF0B1875447D9B6ECD04BCB74B9A1&tagId=f77980b69e0b4eaabe6f8fd859e62a8e
         */
    }
}
