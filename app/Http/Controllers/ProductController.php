<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use inklabs\kommerce\Action\Product\CreateProductCommand;
use inklabs\kommerce\Action\Product\GetProductQuery;
use inklabs\kommerce\Action\Product\Query\GetProductRequest;
use inklabs\kommerce\Action\Product\Query\GetProductResponse;
use inklabs\kommerce\EntityDTO\ProductDTO;

class ProductController extends Controller
{
    public function show($productId)
    {
        echo $productId;

        $request = new GetProductRequest($productId);
        $response = new GetProductResponse($this->getPricing());
        $this->dispatchQuery(new GetProductQuery($request, $response));

        $productDTO = $response->getProductDTOWithAllData();

        echo '<pre>';
        print_r($productDTO);
    }

    public function createDummy()
    {
        $faker = \Faker\Factory::create();

        $productDTO = new ProductDTO();
        $productDTO->name = $faker->name;
        $productDTO->unitPrice = $faker->numberBetween(100, 2000);

        $command = new CreateProductCommand($productDTO);
        $this->dispatch($command);

        echo 'Created: ' . $productDTO->name;
        echo '<p><a href="/p/' . $command->getProductId()->getHex() . '">' . $command->getProductId()->getHex() . '</a></p>';
    }
}
