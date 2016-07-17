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
    public function index()
    {
        $paginationDTO = new PaginationDTO();

        $request = new ListProductsRequest('', $paginationDTO);
        $response = new ListProductsResponse();

        $this->dispatchQuery(new ListProductsQuery($request, $response));

        $productDTOs = $response->getProductDTOs();

        $show = [
            'slug'                  => true,
            'sku'                   => true,
            'name'                  => true,
            'quantity'              => true,
            'unitPrice'             => false,
            'isInventoryRequired'   => false,
            'isPriceVisible'        => false,
            'isActive'              => false,
            'isVisible'             => false,
            'isTaxable'             => false,
            'isShippable'           => false,
            'areAttachmentsEnabled' => false,
        ];

        return view('product.index', compact('productDTOs', 'show'));

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

        $productDTO = $response->getProductDTOWithAllData();

        return view('product.show', compact('productDTO'));
    }

    public function create()
    {
        return view('product.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $productDTO = new ProductDTO();
        $this->buildProduct($request, $productDTO);

        $this->dispatch(new CreateProductCommand($productDTO));

        return redirect()->route('p.show', $productDTO->id);
    }

    public function edit($productId)
    {
        $request = new GetProductRequest($productId);
        $response = new GetProductResponse($this->getPricing());
        $this->dispatchQuery(new GetProductQuery($request, $response));

        $productDTO = $response->getProductDTOWithAllData();

        return view('product.edit', compact('productDTO'));
    }

    public function update(Request $request, $productId)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $productRequest = new GetProductRequest($productId);
        $productResponse = new GetProductResponse($this->getPricing());
        $this->dispatchQuery(new GetProductQuery($productRequest, $productResponse));

        $productDTO = $productResponse->getProductDTOWithAllData();

        $this->buildProduct($request, $productDTO);

        $this->dispatch(new UpdateProductCommand($productDTO));

        return redirect()->route('p.show', $productDTO->id);
    }

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
        $productDTO->slug = $request->get('slug');
        $productDTO->sku = $request->get('sku');
        $productDTO->name = $request->get('name');
        $productDTO->quantity = $request->get('quantity');
        $productDTO->unitPrice = $request->get('price');
        $productDTO->isInventoryRequired = ($request->get('inventory-required') === 'on');
        $productDTO->isPriceVisible = ($request->get('price-visible') === 'on');
        $productDTO->isActive = ($request->get('active') === 'on');
        $productDTO->isVisible = ($request->get('visible') === 'on');
        $productDTO->isTaxable = ($request->get('taxable') === 'on');
        $productDTO->isShippable = ($request->get('shippable') === 'on');
        $productDTO->areAttachmentsEnabled = ($request->get('attachments-enabled') === 'on');
    }

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
        echo '<p><a href="/p/' . $command->getProductId() . '">' . $command->getProductId()->getHex() . '</a></p>';

        dd($productDTO);
    }
}
