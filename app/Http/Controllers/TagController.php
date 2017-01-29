<?php
namespace App\Http\Controllers;

use inklabs\kommerce\Action\Product\GetProductsByTagQuery;
use inklabs\kommerce\Action\Product\Query\GetProductsByTagRequest;
use inklabs\kommerce\Action\Product\Query\GetProductsByTagResponse;
use inklabs\kommerce\Action\Tag\GetTagQuery;
use inklabs\kommerce\Action\Tag\ListTagsQuery;
use inklabs\kommerce\Action\Tag\Query\GetTagRequest;
use inklabs\kommerce\Action\Tag\Query\GetTagResponse;
use inklabs\kommerce\Action\Tag\Query\ListTagsRequest;
use inklabs\kommerce\Action\Tag\Query\ListTagsResponse;

class TagController extends Controller
{
    public function show($slug, $tagId)
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

        return $this->renderTemplate(
            '@store/tag/show.twig',
            [
                'tag' => $tagDTO,
                'products' => $productDTOs,
                'pagination' => $paginationDTO,
            ]
        );
    }

    public function getList()
    {
        $request = new ListTagsRequest(
            null,
            $this->getPaginationDTO(12)
        );
        $response = new ListTagsResponse($this->getPricing());
        $this->dispatchQuery(new ListTagsQuery($request, $response));

        $tagDTOs = $response->getTagDTOs();
        $paginationDTO = $response->getPaginationDTO();

        return $this->renderTemplate(
            '@store/tag/list.twig',
            [
                'tags' => $tagDTOs,
                'pagination' => $paginationDTO,
            ]
        );
    }
}
