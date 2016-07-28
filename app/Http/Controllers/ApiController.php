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

    public function processQuery(Request $request, $model, $action, $method)
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

        $data = $this->getDataFromMethods($method, $responseObject);

        return response()->json($data);

        /**
         * Example:
         *  http://localhost:8000/api/v1/Product/GetRandomProductsQuery/getProductDTOs?limit=5
         *  http://localhost:8000/api/v1/Product/GetProductQuery/getProductDTOWithAllData?id=63FBF0B1875447D9B6ECD04BCB74B9A1
         */
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

            if (is_array($value)) {
                $entityDTOClassName = '\\inklabs\\kommerce\\EntityDTO\\' . $key;
                $entityDTO = new $entityDTOClassName;
                foreach ($value as $k => $v) {
                    $entityDTO->$k = $v;
                }
                $value = $entityDTO;
            }

            $constructorParameters[] = $value;
        }

        if (! empty($constructorParameters)) {
            $requestObject = $reflection->newInstanceArgs($constructorParameters);
        } else {
            $requestObject = $reflection->newInstance();
        }

        return $requestObject;
    }

    public function processCommand(Request $request, $model, $action, $method = '')
    {
        $post = $request->query();

        $commandClassName = '\\inklabs\\kommerce\\Action\\' . $model . '\\' . $action;

        $commandObject = $this->getDynamicObject($commandClassName, $post);

        try {
            $this->dispatch($commandObject);
        } catch (Exception $e) {
            if ($e instanceof KommerceException) {
                $safeMessage = $e->getMessage();
            } else {
                $safeMessage = 'Unrecoverable error';
            }

            return [
                'isSuccess' => false,
                'message' => $safeMessage,
                'data' => [],
            ];
        }

        $data = $this->getDataFromMethods($method, $commandObject);

        return response()->json([
            'isSuccess' => true,
            'data' => $data,
        ]);

        /**
         * Example:
         *  http://localhost:8000/api/v1/Product/AddTagToProductCommand?productId=63FBF0B1875447D9B6ECD04BCB74B9A1&tagId=f77980b69e0b4eaabe6f8fd859e62a8e
         *  http://localhost:8000/api/v1/Product/RemoveTagFromProductCommand?productId=63FBF0B1875447D9B6ECD04BCB74B9A1&tagId=f77980b69e0b4eaabe6f8fd859e62a8e
         */
    }

    /**
     * @param $method
     * @param $commandObject
     * @return array
     */
    private function getDataFromMethods($method, $commandObject)
    {
        $methods = explode('_', $method);
        $data = [];
        foreach ($methods as $method) {
            if (empty($method)) {
                continue;
            }
            $data[$method] = $commandObject->$method();
        }
        return $data;
    }
}
