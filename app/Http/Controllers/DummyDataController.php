<?php
namespace App\Http\Controllers;

use inklabs\kommerce\Action\Option\CreateOptionCommand;
use inklabs\kommerce\Action\Option\CreateOptionProductCommand;
use inklabs\kommerce\Action\Option\CreateOptionValueCommand;
use inklabs\kommerce\Action\Product\AddTagToProductCommand;
use inklabs\kommerce\Action\Product\CreateProductCommand;
use inklabs\kommerce\Action\Product\GetProductQuery;
use inklabs\kommerce\Action\Product\Query\GetProductRequest;
use inklabs\kommerce\Action\Product\Query\GetProductResponse;
use inklabs\kommerce\Action\Tag\AddOptionToTagCommand;
use inklabs\kommerce\Action\Tag\AddTextOptionToTagCommand;
use inklabs\kommerce\Action\Tag\CreateTagCommand;
use inklabs\kommerce\Action\Tag\GetTagQuery;
use inklabs\kommerce\Action\Tag\Query\GetTagRequest;
use inklabs\kommerce\Action\Tag\Query\GetTagResponse;
use inklabs\kommerce\Action\Option\CreateTextOptionCommand;
use inklabs\kommerce\Entity\TextOptionType;
use inklabs\kommerce\EntityDTO\OptionDTO;
use inklabs\kommerce\EntityDTO\OptionProductDTO;
use inklabs\kommerce\EntityDTO\OptionValueDTO;
use inklabs\kommerce\EntityDTO\ProductDTO;
use inklabs\kommerce\EntityDTO\TagDTO;

class DummyDataController extends Controller
{
    const PAGINATION_STRING = 'PaginationDTO[maxResults]=5&PaginationDTO[page]=1';

    public function createDummyProduct()
    {
        $productDTO = $this->getDummyProduct();
        $tagDTO = $this->getDummyTag();

        $optionShirtSizeId = $this->getDummyOptionId('Shirt Size');
        $optionValueId = $this->getDummyOptionValueId($optionShirtSizeId);

        $optionStickerId = $this->getDummyOptionId('Heat Transfer Sticker');
        $productDTOSticker1 = $this->getDummyProduct('Chicago Bears');
        $productDTOSticker2 = $this->getDummyProduct('Green Bay Packers');
        $this->createDummyOptionProduct(
            $optionStickerId,
            $productDTOSticker1->id->getHex()
        );
        $this->createDummyOptionProduct(
            $optionStickerId,
            $productDTOSticker2->id->getHex()
        );

        $textOptionId = $this->getDummyTextOptionId('Enscription Message');

        $productId = $productDTO->id->getHex();
        $tagId = $tagDTO->id->getHex();

        $this->addTagToProduct($productId, $tagId);
        $this->addOptionToTag($tagId, $optionShirtSizeId);
        $this->addOptionToTag($tagId, $optionStickerId);
        $this->addTextOptionToTag($tagId, $textOptionId);

        $productUrl = route(
            'product.show',
            [
                'slug' => $productDTO->slug,
                'productId' => $productId,
            ]
        );

        $pagination = self::PAGINATION_STRING;

        echo <<<HEREDOC
        <h3>Created:</h3>
        <ul>
            <li>Product: {$productId}</li>
            <li>Tag: {$tagId}</li>
            <li>Option: {$optionShirtSizeId}</li>
            <li>OptionValue: {$optionValueId}</li>
            <li><a href="{$productUrl}">View Product</a></li>
            <li><a href="">Create another dummy Product and Tag</a></li>
        </ul>

        <h3>Queries</h3>
        <ul>
            <li>Product:
                <ul>
                    <li><a href="/api/v1/Product/GetRandomProductsQuery/getProductDTOs?limit=5">GetRandomProductsQuery</a></li>
                    <li><a href="/api/v1/Product/ListProductsQuery/getProductDTOs_getPaginationDTO?query=&{$pagination}">ListProductsQuery</a></li>
                    <li><a href="/api/v1/Product/GetRelatedProductsQuery/getProductDTOs?productIds[]={$productId}&limit=5">GetRelatedProductsQuery</a></li>
                    <li><a href="/api/v1/Product/GetProductsByIdsQuery/getProductDTOs?productIds[]={$productId}">GetProductsByIdsQuery</a></li>
                    <li><a href="/api/v1/Product/GetProductsByTagQuery/getProductDTOs_getPaginationDTO?tagId={$tagId}&{$pagination}">GetProductsByTagQuery</a></li>
                    <li><a href="/api/v1/Product/GetProductQuery/getProductDTOWithAllData?id={$productId}">GetProductQuery - getProductDTOWithAllData</a></li>
                    <li><a href="/api/v1/Product/GetProductQuery/getProductDTO?id={$productId}">GetProductQuery - getProductDTO</a></li>
                </ul>
            </li>
            <li>Tag:
                <ul>
                    <li><a href="/api/v1/Tag/GetTagQuery/getTagDTOWithAllData?id={$tagId}">GetTagQuery - getTagDTOWithAllData</a></li>
                    <li><a href="/api/v1/Tag/GetTagQuery/getTagDTO?id={$tagId}">GetTagQuery - getTagDTO</a></li>
                </ul>
            </li>
            <li>Option:
                <ul>
                    <li><a href="/api/v1/Option/ListOptionsQuery/getOptionDTOs_getPaginationDTO?query=&{$pagination}">ListOptionsQuery</a></li>
                    <li><a href="/api/v1/Option/GetOptionQuery/getOptionDTOWithAllData?id={$optionShirtSizeId}">GetOptionQuery - getOptionDTOWithAllData</a></li>
                    <li><a href="/api/v1/Option/GetOptionQuery/getOptionDTO?id={$optionShirtSizeId}">GetOptionQuery - getOptionDTO</a></li>
                </ul>
            </li>
            <li>TextOption:
                <ul>
                    <li><a href="/api/v1/Option/ListTextOptionsQuery/getTextOptionDTOs_getPaginationDTO?query=&{$pagination}">ListTextOptionsQuery</a></li>
                    <li><a href="/api/v1/Option/GetTextOptionQuery/getTextOptionDTOWithAllData?id={$textOptionId}">GetTextOptionQuery - getTextOptionDTOWithAllData</a></li>
                    <li><a href="/api/v1/TextOption/GetTextOptionQuery/getTextOptionDTO?id={$textOptionId}">GetTextOptionQuery - getTextOptionDTO</a></li>
                </ul>
            </li>
        </ul>

        <h3>Commands</h3>
        <ul>
            <li><a href="/api/v1/Product/AddTagToProductCommand?productId={$productId}&tagId={$tagId}">AddTagToProductCommand</a></li>
            <li><a href="/api/v1/Product/RemoveTagFromProductCommand?productId={$productId}&tagId={$tagId}">RemoveTagFromProductCommand</a></li>
            <li><a href="/api/v1/Product/DeleteProductCommand?productId={$productId}">DeleteProductCommand</a></li>
            <li><a href="/api/v1/Option/DeleteOptionCommand?optionId={$optionShirtSizeId}">DeleteOptionCommand</a></li>
        </ul>

HEREDOC;
    }

    /**
     * @return $productDTO
     */
    protected function getDummyProduct($name = null)
    {
        $faker = \Faker\Factory::create();

        if ($name === null) {
            $name = $faker->name;
        }

        $productDTO = new ProductDTO();
        $productDTO->name = $name;
        $productDTO->description = $faker->paragraph(5);
        $productDTO->defaultImage = $faker->imageUrl();
        $productDTO->sku = $faker->randomNumber(5);
        $productDTO->unitPrice = $faker->numberBetween(100, 2000);
        $productDTO->isVisible = true;
        $productDTO->isActive = true;
        $productDTO->rating = $faker->numberBetween(100, 500);

        $command = new CreateProductCommand($productDTO);
        $this->dispatch($command);

        $productId = $command->getProductId()->getHex();

        $request = new GetProductRequest($productId);
        $response = new GetProductResponse($this->getPricing());
        $this->dispatchQuery(new GetProductQuery($request, $response));

        return $response->getProductDTO();
    }

    /**
     * @return TagDTO
     */
    protected function getDummyTag()
    {
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

        $request = new GetTagRequest($tagId);
        $response = new GetTagResponse($this->getPricing());
        $this->dispatchQuery(new GetTagQuery($request, $response));

        return $response->getTagDTO();
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getDummyOptionId($name)
    {
        $faker = \Faker\Factory::create();

        $optionDTO = new OptionDTO();

        $optionDTO->name = $name;
        $optionDTO->description = $faker->paragraph(3);
        $optionDTO->sortOrder = 0;

        $command = new CreateOptionCommand($optionDTO);
        $this->dispatch($command);

        return $command->getOptionId()->getHex();
    }

    /**
     * @param $optionId
     * @return string
     */
    protected function getDummyOptionValueId($optionId)
    {
        $faker = \Faker\Factory::create();

        $optionValueDTO = new OptionValueDTO();
        $optionValueDTO->name = 'Large';
        $optionValueDTO->sku = 'ML';
        $optionValueDTO->unitPrice = $faker->numberBetween(100, 1000);
        $optionValueDTO->shippingWeight = $faker->numberBetween(10, 40);
        $optionValueDTO->sortOrder = 0;

        $command = new CreateOptionValueCommand($optionId, $optionValueDTO);
        $this->dispatch($command);

        return $command->getOptionId()->getHex();
    }

    /**
     * @param string $optionId
     * @param string $productId
     * @return OptionProductDTO
     */
    private function createDummyOptionProduct($optionId, $productId)
    {
        $optionProductDTO = new OptionProductDTO();
        $optionProductDTO->sortOrder = 0;

        $command = new CreateOptionProductCommand($optionId, $productId, $optionProductDTO);
        $this->dispatch($command);
    }

    /**
     * @param string $name
     * @return string
     */
    private function getDummyTextOptionId($name)
    {
        $faker = \Faker\Factory::create();

        $description = $faker->paragraph(3);
        $sortOrder = 0;
        $textOptionTypeId = TextOptionType::TEXT;

        $command = new CreateTextOptionCommand(
            $name,
            $description,
            $sortOrder,
            $textOptionTypeId
        );
        $this->dispatch($command);

        return $command->getTextOptionId()->getHex();
    }

    private function addTagToProduct($productId, $tagId)
    {
        $this->dispatch(
            new AddTagToProductCommand($productId, $tagId)
        );
    }

    private function addOptionToTag($tagId, $optionId)
    {
        $this->dispatch(
            new AddOptionToTagCommand($tagId, $optionId)
        );
    }

    private function addTextOptionToTag($tagId, $textOptionId)
    {
        $this->dispatch(
            new AddTextOptionToTagCommand($tagId, $textOptionId)
        );
    }
}
