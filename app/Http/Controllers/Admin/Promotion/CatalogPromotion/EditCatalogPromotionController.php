<?php
namespace App\Http\Controllers\Admin\Promotion\CatalogPromotion;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\CatalogPromotion\UpdateCatalogPromotionCommand;
use inklabs\kommerce\Entity\PromotionType;
use inklabs\kommerce\Exception\EntityValidatorException;

class EditCatalogPromotionController extends Controller
{
    public function get($catalogPromotionId)
    {
        $catalogPromotion = $this->getCatalogPromotionWithAllData($catalogPromotionId);

        return $this->renderTemplate(
            '@admin/promotion/catalog-promotion/edit.twig',
            [
                'catalogPromotion' => $catalogPromotion,
                'promotionTypes' => PromotionType::getSlugNameMap(),
            ]
        );
    }

    public function post(Request $request)
    {
        $catalogPromotionId = $request->input('catalogPromotionId');
        $catalogPromotionValues = $request->input('catalogPromotion');
        $promotionTypeSlug = Arr::get($catalogPromotionValues, 'type');
        $value = Arr::get($catalogPromotionValues, 'value');

        if ($promotionTypeSlug === 'fixed' || $promotionTypeSlug === 'exact') {
            $value = (int) ($value * 100);
        }

        $name = trim(Arr::get($catalogPromotionValues, 'name'));
        $maxRedemptions = (int) Arr::get($catalogPromotionValues, 'maxRedemptions');
        $reducesTaxSubtotal = (bool) Arr::get($catalogPromotionValues, 'reducesTaxSubtotal', false);
        $tagId = $this->getStringOrNull(Arr::get($catalogPromotionValues, 'tagId'));

        $startAt = $this->getTimestampFromDateTimeTimezoneInput($catalogPromotionValues['start']);
        $endAt = $this->getTimestampFromDateTimeTimezoneInput($catalogPromotionValues['end']);

        try {
            $this->dispatch(new UpdateCatalogPromotionCommand(
                $name,
                $promotionTypeSlug,
                $value,
                $reducesTaxSubtotal,
                $maxRedemptions,
                $startAt,
                $endAt,
                $catalogPromotionId,
                $tagId
            ));

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

        return $this->get($catalogPromotionId);
    }
}
