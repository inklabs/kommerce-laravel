<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use ReflectionClass;

class ApiController extends Controller
{
    public function process(Request $request, $model, $action)
    {
        $query = $request->query();

        $queryActionName = $action;
        $requestActionName = str_replace('Query', 'Request', $queryActionName);
        $responseActionName = str_replace('Query', 'Response', $queryActionName);

        $queryClassName = '\\inklabs\\kommerce\\Action\\' . $model . '\\' . $queryActionName;
        $requestClassName = '\\inklabs\\kommerce\\Action\\' . $model . '\\Query\\' . $requestActionName;
        $responseClassName = '\\inklabs\\kommerce\\Action\\' . $model . '\\Query\\' . $responseActionName;

        $requestObject = $this->getRequestObject($requestClassName, $query);

        $responseObject = new $responseClassName($this->getPricing());
        $queryObject = new $queryClassName($requestObject, $responseObject);

        $this->dispatchQuery($queryObject);

        if (method_exists($responseObject, 'getProductDTO')) {
            $jsonData = json_encode($responseObject->getProductDTO());
        } elseif (method_exists($responseObject, 'getProductDTOs')) {
            $jsonData = json_encode($responseObject->getProductDTOs());
        } else {
            $jsonData = '<img src="http://i.imgur.com/bq4XXwm.png">';
        }

        /**
         * Example:
         *  http://localhost:8000/api/v1/Product/GetRandomProductsQuery?limit=5
         *  http://localhost:8000/api/v1/Product/GetProductQuery?id=63FBF0B1875447D9B6ECD04BCB74B9A1
         */

        echo '<pre>';
        print_r([
            $model,
            $action,
            $query,
            $jsonData,
        ]);
    }

    /**
     * @param string $requestClassName
     * @param array $query
     * @return mixed
     */
    private function getRequestObject($requestClassName, $query)
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
}
