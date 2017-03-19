<?php
namespace App\Http\Controllers;

use inklabs\kommerce\Action\Product\GetProductsByTagQuery;
use inklabs\kommerce\Action\Product\Query\GetProductsByTagRequest;
use inklabs\kommerce\ActionResponse\Product\GetProductsByTagResponse;
use inklabs\kommerce\Action\Tag\ListTagsQuery;
use inklabs\kommerce\ActionResponse\Tag\ListTagsResponse;

class TagController extends Controller
{
    public function show($slug, $tagId)
    {
        $tagDTO = $this->getTagWithAllData($tagId);

        if ($slug !== $tagDTO->slug) {
            return redirect()->route(
                'tag.show',
                [
                    'slug' => $tagDTO->slug,
                    'tagId' => $tagDTO->id->getHex(),
                ]
            );
        }

        /** @var GetProductsByTagResponse $response */
        $response = $this->dispatchQuery(new GetProductsByTagQuery(
            $tagDTO->id->getHex(),
            $this->getPaginationDTO(12)
        ));

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
        /** @var ListTagsResponse $response */
        $response = $this->dispatchQuery(new ListTagsQuery(
            null,
            $this->getPaginationDTO(12)
        ));

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
