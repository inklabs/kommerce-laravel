<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use inklabs\kommerce\Action\Product\CreateProductCommand;
use inklabs\kommerce\Action\Product\DeleteProductCommand;
use inklabs\kommerce\Action\Product\GetProductQuery;
use inklabs\kommerce\Action\Product\ListProductsQuery;
use inklabs\kommerce\Action\Product\Query\GetProductRequest;
use inklabs\kommerce\Action\Product\Query\GetProductResponse;
use inklabs\kommerce\Action\Product\Query\ListProductsRequest;
use inklabs\kommerce\Action\Product\Query\ListProductsResponse;
use inklabs\kommerce\Action\Product\UpdateProductCommand;
use inklabs\kommerce\EntityDTO\PaginationDTO;
use inklabs\kommerce\EntityDTO\ProductDTO;

class ProductController extends Controller
{
    /**
     * @param null $query
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = $request->has('q') ? $request->get('q') : null;

        $paginationDTO = new PaginationDTO();

        $productRequest = new ListProductsRequest($query, $paginationDTO);
        $response = new ListProductsResponse();

        $this->dispatchQuery(new ListProductsQuery($productRequest, $response));

        $data = $response->getProductDTOs();

        return $data;

    }

    /**
     * @param $productId
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($productId)
    {
        $request = new GetProductRequest($productId);
        $response = new GetProductResponse($this->getPricing());
        $this->dispatchQuery(new GetProductQuery($request, $response));

        $data = $response->getProductDTOWithAllData();

        return [$data];

    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $productDTO = new ProductDTO();
        $this->buildProduct($request, $productDTO);

        $this->dispatch(new CreateProductCommand($productDTO));

//        return redirect()->route('p.show', $productDTO->id);
    }

    /**
     * @param $productId
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($productId)
    {
        $request = new GetProductRequest($productId);
        $response = new GetProductResponse($this->getPricing());
        $this->dispatchQuery(new GetProductQuery($request, $response));

        $productDTO = $response->getProductDTOWithAllData();

//        return view('product.edit', compact('productDTO'));
    }

    /**
     * @param Request $request
     * @param         $productId
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $productId)
    {
        $this->validate($request, [
            'name' => 'required|min:2'
        ]);

        $productRequest = new GetProductRequest($productId);
        $productResponse = new GetProductResponse($this->getPricing());
        $this->dispatchQuery(new GetProductQuery($productRequest, $productResponse));

        $productDTO = $productResponse->getProductDTOWithAllData();

        $this->buildProduct($request, $productDTO);

        $this->dispatch(new UpdateProductCommand($productDTO));

//        return redirect()->route('p.show', $productDTO->id);
    }

    /**
     * Delete Product
     * @param $productId
     */
    public function destroy($productId)
    {
        $this->dispatch(new DeleteProductCommand($productId));
    }

    /**
     * @param Request $request
     * @param         $productDTO
     */
    public function buildProduct($request, $productDTO)
    {

        if ($request->has('slug')) {
            $productDTO->slug = $request->get('slug');
        }

        if ($request->has('sku')) {
            $productDTO->sku = $request->get('sku');
        }

        if ($request->has('name')) {
            $productDTO->name = $request->get('name');
        }

        if ($request->has('quantity')) {
            $productDTO->quantity = $request->get('quantity');
        }

        if ($request->has('price')) {
            $productDTO->unitPrice = $request->get('price');
        }

        if ($request->has('inventory-required')) {
            $productDTO->isInventoryRequired = $request->get('inventory-required');
        }

        if ($request->has('price-visible')) {
            $productDTO->isPriceVisible = $request->get('price-visible');
        }

        if ($request->has('active')) {
            $productDTO->isActive = $request->get('active');
        }

        if ($request->has('visible')) {
            $productDTO->isVisible = $request->get('visible');
        }

        if ($request->has('taxable')) {
            $productDTO->isTaxable = $request->get('taxable');
        }

        if ($request->has('shippable')) {
            $productDTO->isShippable = $request->get('shippable');
        }

        if ($request->has('attachments-enabled')) {
            $productDTO->areAttachmentsEnabled = $request->get('attachments-enabled');
        }

        if ($request->hasFile('default-image')) {
            $productDTO->defaultImage = $request->file('default-image');
        }

        if ($request->has('quantity')) {
            $productDTO->quantity = $request->get('quantity');
        }

        if ($request->has('shipping-weight')) {
            $productDTO->shippingWeight = $request->get('shipping-weight');
        }

    }

    /**
     * Generate a product
     */
    public function createDummy()
    {
        $faker = \Faker\Factory::create();

        $productDTO = new ProductDTO();
        $productDTO->name = $faker->name;
        $productDTO->sku = $faker->md5;
        $productDTO->unitPrice = $faker->numberBetween(100, 2000);

        $command = new CreateProductCommand($productDTO);
        $this->dispatch($command);

        echo 'Created: ' . $productDTO->name;
//        echo '<p><a href="/p/' . $command->getProductId() . '">' . $command->getProductId()->getHex() . '</a></p>';

        dd($productDTO);
    }
}
