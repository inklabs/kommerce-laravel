<?php
namespace App\Http\Controllers\Admin\Promotion\CatalogPromotion;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\CatalogPromotion\DeleteCatalogPromotionCommand;
use inklabs\kommerce\Exception\KommerceException;

class DeleteCatalogPromotionController extends Controller
{
    public function post(Request $request)
    {
        $catalogPromotionId = $request->input('catalogPromotionId');

        try {
            $this->dispatch(new DeleteCatalogPromotionCommand($catalogPromotionId));
            $this->flashSuccess('Success removing catalog promotion');
        } catch (KommerceException $e) {
            $this->flashError('Unable remove catalog promotion.');
        }

        return redirect()->route('admin.catalog-promotion');
    }
}
