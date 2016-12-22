<?php
namespace App\Http\Controllers\Admin\Promotion\CatalogPromotion;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\CatalogPromotion\UpdateCatalogPromotionCommand;
use inklabs\kommerce\Entity\PromotionType;
use inklabs\kommerce\EntityDTO\CatalogPromotionDTO;
use inklabs\kommerce\Exception\EntityValidatorException;

class EditCatalogPromotionController extends Controller
{
    public function get($catalogPromotionId)
    {
        $catalogPromotion = $this->getCatalogPromotion($catalogPromotionId);

        return $this->renderTemplate(
            '@theme/admin/promotion/catalog-promotion/edit.twig',
            [
                'catalogPromotion' => $catalogPromotion,
            ]
        );
    }

    public function post(Request $request)
    {
        $catalogPromotionId = $request->input('catalogPromotionId');
        $catalogPromotion = $this->getCatalogPromotion($catalogPromotionId);

        $this->updateCatalogPromotionDTOFromPost($catalogPromotion, $request->input('catalogPromotion'));

        try {
            $this->dispatch(new UpdateCatalogPromotionCommand($catalogPromotion));

            $this->flashSuccess('CatalogPromotion has been saved.');
            return redirect()->route(
                'admin.catalog-promotion.edit',
                [
                    'catalogPromotionId' => $catalogPromotionId,
                ]
            );
        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to save catalogPromotion!');
            $this->flashFormErrors($e->getErrors());
        }
    }

    private function updateCatalogPromotionDTOFromPost(CatalogPromotionDTO & $catalogPromotionDTO, array $catalogPromotionValues)
    {
        $typeId = (int) Arr::get($catalogPromotionValues, 'type');
        $promotionType = PromotionType::createById($typeId);
        $promotionTypeDTO = $this->getDTOBuilderFactory()
            ->getPromotionTypeDTOBuilder($promotionType)
            ->build();

        if ($promotionTypeDTO->isFixed || $promotionTypeDTO->isExact) {
            $value = (int) (Arr::get($catalogPromotionValues, 'value') * 100);
        } else {
            $value = (int) Arr::get($catalogPromotionValues, 'value');
        }

        $catalogPromotionDTO->name = trim(Arr::get($catalogPromotionValues, 'name'));
        $catalogPromotionDTO->type = $promotionTypeDTO;
        $catalogPromotionDTO->value = $value;
        $catalogPromotionDTO->maxRedemptions = (int) Arr::get($catalogPromotionValues, 'maxRedemptions');
        $catalogPromotionDTO->reducesTaxSubtotal = Arr::get($catalogPromotionValues, 'reducesTaxSubtotal', false);

        // TODO:
        //$catalogPromotionDTO->start =
        //$catalogPromotionDTO->end =
        //$catalogPromotionDTO->tag =
    }
}
