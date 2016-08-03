<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use inklabs\kommerce\Action\Option\CreateOptionCommand;
use inklabs\kommerce\Action\Option\CreateOptionValueCommand;
use inklabs\kommerce\Action\Option\GetOptionQuery;
use inklabs\kommerce\Action\Option\Query\GetOptionRequest;
use inklabs\kommerce\Action\Option\Query\GetOptionResponse;
use inklabs\kommerce\Action\Product\CreateProductCommand;
use inklabs\kommerce\Action\Product\GetProductQuery;
use inklabs\kommerce\Action\Product\Query\GetProductRequest;
use inklabs\kommerce\Action\Product\Query\GetProductResponse;
use inklabs\kommerce\Action\Tag\CreateTagCommand;
use inklabs\kommerce\EntityDTO\OptionDTO;
use inklabs\kommerce\EntityDTO\OptionValueDTO;
use inklabs\kommerce\EntityDTO\ProductDTO;
use inklabs\kommerce\EntityDTO\TagDTO;

class DummyDataController extends Controller
{
    const PAGINATION_STRING = 'PaginationDTO[maxResults]=5&PaginationDTO[page]=1';

    public function createDummyProduct()
    {
        $faker = \Faker\Factory::create();

        $productDTO = new ProductDTO();
        $productDTO->name = $faker->name;
        $productDTO->description = $faker->paragraph(5);
        $productDTO->defaultImage = $faker->imageUrl();
        $productDTO->sku = $faker->md5;
        $productDTO->unitPrice = $faker->numberBetween(100, 2000);
        $productDTO->isVisible = true;
        $productDTO->isActive = true;

        $command = new CreateProductCommand($productDTO);
        $this->dispatch($command);

        $productId = $command->getProductId()->getHex();

        $request = new GetProductRequest($productId);
        $response = new GetProductResponse($this->getPricing());
        $this->dispatchQuery(new GetProductQuery($request, $response));

        $productDTO = $response->getProductDTO();

        $faker = \Faker\Factory::create();

        $tagDTO = new TagDTO();
        $tagDTO->name = $faker->name;
        $tagDTO->description = $faker->paragraph(5);
        $tagDTO->isVisible = true;
        $tagDTO->isActive = true;
        $tagDTO->sortOrder = 0;

        $command = new CreateTagCommand($tagDTO);
        $this->dispatch($command);

        $tagId = $command->getTagId()->getHex();

        $pagination = self::PAGINATION_STRING;

        $productUrl = route(
            'product.show',
            [
                'slug' => $productDTO->slug,
                'productId' => $productDTO->id->getHex(),
            ]
        );

        echo <<<HEREDOC
        <h3>Created:</h3>
        <ul>
            <li>Product: {$productId}</li>
            <li>Tag: {$tagId}</li>
            <li><a href="{$productUrl}">View Product</a></li>
            <li><a href="">Create another dummy Product and Tag</a></li>
        </ul>

        <h3>Queries</h3>
        <ul>
            <li><a href="/api/v1/Product/GetRandomProductsQuery/getProductDTOs?limit=5">GetRandomProductsQuery</a></li>
            <li><a href="/api/v1/Product/ListProductsQuery/getProductDTOs_getPaginationDTO?query=&{$pagination}">ListProductsQuery</a></li>
            <li><a href="/api/v1/Product/GetRelatedProductsQuery/getProductDTOs?productIds[]={$productId}&limit=5">GetRelatedProductsQuery</a></li>
            <li><a href="/api/v1/Product/GetProductsByIdsQuery/getProductDTOs?productIds[]={$productId}">GetProductsByIdsQuery</a></li>
            <li><a href="/api/v1/Product/GetProductsByTagQuery/getProductDTOs_getPaginationDTO?tagId={$tagId}&{$pagination}">GetProductsByTagQuery</a></li>
            <li><a href="/api/v1/Product/GetProductQuery/getProductDTOWithAllData?id={$productId}">GetProductQuery - getProductDTOWithAllData</a></li>
            <li><a href="/api/v1/Product/GetProductQuery/getProductDTO?id={$productId}">GetProductQuery - getProductDTO</a></li>
            <li><a href="/api/v1/Tag/GetTagQuery/getTagDTOWithAllData?id={$tagId}">GetTagQuery - getTagDTOWithAllData</a></li>
            <li><a href="/api/v1/Tag/GetTagQuery/getTagDTO?id={$tagId}">GetTagQuery - getTagDTO</a></li>
        </ul>

        <h3>Commands</h3>
        <ul>
            <li><a href="/api/v1/Product/AddTagToProductCommand?productId={$productId}&tagId={$tagId}">AddTagToProductCommand</a></li>
            <li><a href="/api/v1/Product/RemoveTagFromProductCommand?productId={$productId}&tagId={$tagId}">RemoveTagFromProductCommand</a></li>
            <li><a href="/api/v1/Product/DeleteProductCommand?productId={$productId}">DeleteProductCommand</a></li>
        </ul>

HEREDOC;
    }

    public function createDummyOption()
    {
        $faker = \Faker\Factory::create();

        $optionDTO = new OptionDTO();
        $optionDTO->name = 'Shirt Size';
        $optionDTO->description = $faker->paragraph(3);
        $optionDTO->sortOrder = 0;

        $command = new CreateOptionCommand($optionDTO);
        $this->dispatch($command);

        $optionId = $command->getOptionId()->getHex();

        $request = new GetOptionRequest($optionId);
        $response = new GetOptionResponse($this->getPricing());
        $this->dispatchQuery(new GetOptionQuery($request, $response));

        $optionDTO = $response->getOptionDTO();

        $faker = \Faker\Factory::create();

        $optionValueDTO = new OptionValueDTO();
        $optionValueDTO->name = 'L';
        $optionValueDTO->sku = 'ML';
        $optionValueDTO->unitPrice = $faker->numberBetween(100, 1000);
        $optionValueDTO->shippingWeight = $faker->numberBetween(10, 40);
        $optionValueDTO->sortOrder = 0;

        $command = new CreateOptionValueCommand($optionId, $optionValueDTO);
        $this->dispatch($command);

        $optionValueId = $command->getOptionId()->getHex();

        $pagination = self::PAGINATION_STRING;

        echo <<<HEREDOC
        <h3>Created:</h3>
        <ul>
            <li>Option: {$optionId}</li>
            <li>OptionValue: {$optionValueId}</li>
            <li><a href="">Create another dummy Option and Option Value</a></li>
        </ul>

        <h3>Queries</h3>
        <ul>
            <li><a href="/api/v1/Option/ListOptionsQuery/getOptionDTOs_getPaginationDTO?query=&{$pagination}">ListOptionsQuery</a></li>
            <li><a href="/api/v1/Option/GetOptionQuery/getOptionDTOWithAllData?id={$optionId}">GetOptionQuery - getOptionDTOWithAllData</a></li>
            <li><a href="/api/v1/Option/GetOptionQuery/getOptionDTO?id={$optionId}">GetOptionQuery - getOptionDTO</a></li>
        </ul>

        <h3>Commands</h3>
        <ul>
            <li><a href="/api/v1/Option/DeleteOptionCommand?optionId={$optionId}">DeleteOptionCommand</a></li>
        </ul>

HEREDOC;
    }
}
