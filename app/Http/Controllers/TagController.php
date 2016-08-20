<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Product\GetProductsByTagQuery;
use inklabs\kommerce\Action\Product\Query\GetProductsByTagRequest;
use inklabs\kommerce\Action\Product\Query\GetProductsByTagResponse;
use inklabs\kommerce\Action\Tag\GetTagQuery;
use inklabs\kommerce\Action\Tag\Query\GetTagRequest;
use inklabs\kommerce\Action\Tag\Query\GetTagResponse;

class TagController extends Controller
{
    /**
     * @param Request $httpRequest
     * @param string $slug
     * @param string $tagId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function show(Request $httpRequest, $slug, $tagId)
    {
        $request = new GetTagRequest($tagId);
        $response = new GetTagResponse($this->getPricing());
        $this->dispatchQuery(new GetTagQuery($request, $response));

        $tagDTO = $response->getTagDTOWithAllData();

        if ($slug !== $tagDTO->slug) {
            return redirect()->route(
                'tag.show',
                [
                    'slug' => $tagDTO->slug,
                    'tagId' => $tagDTO->id->getHex(),
                ]
            );
        }

        $request = new GetProductsByTagRequest(
            $tagDTO->id->getHex(),
            $this->getPaginationDTO(12)
        );
        $response = new GetProductsByTagResponse($this->getPricing());
        $this->dispatchQuery(new GetProductsByTagQuery($request, $response));

        $productDTOs = $response->getProductDTOs();
        $paginationDTO = $response->getPaginationDTO();

        $this->displayTemplate(
            'tag/show.twig',
            [
                'tag' => $tagDTO,
                'products' => $productDTOs,
                'pagination' => $paginationDTO,
            ]
        );
    }
}
