<?php
namespace App\Http\Controllers\Admin\Promotion\CatalogPromotion;

use App\Http\Controllers\Controller;
use App\Lib\Arr;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\CatalogPromotion\CreateCatalogPromotionCommand;
use inklabs\kommerce\Entity\PromotionType;
use inklabs\kommerce\Exception\EntityValidatorException;

class CreateCatalogPromotionController extends Controller
{
    public function get()
    {
        return $this->renderTemplate(
            '@admin/promotion/catalog-promotion/new.twig',
            [
                'promotionTypes' => PromotionType::getSlugNameMap(),
            ]
        );
    }

    public function post(Request $request)
    {
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
            $command = new CreateCatalogPromotionCommand(
                $name,
                $promotionTypeSlug,
                $value,
                $reducesTaxSubtotal,
                $maxRedemptions,
                $startAt,
                $endAt,
                $tagId
            );
            $this->dispatch($command);

            $this->flashSuccess('Catalog Promotion has been created.');
            return redirect()->route(
                'admin.catalog-promotion.edit',
                [
                    'catalogPromotionId' => $command->getCatalogPromotionId()->getHex(),
                ]
            );
        } catch (EntityValidatorException $e) {
            $this->flashError('Unable to create catalogPromotion!');
            $this->flashFormErrors($e->getErrors());
        }

        return $this->get();
    }
}
