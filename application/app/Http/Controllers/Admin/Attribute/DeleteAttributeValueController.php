<?php
namespace App\Http\Controllers\Admin\Attribute;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Attribute\DeleteAttributeValueCommand;
use inklabs\kommerce\Exception\KommerceException;

class DeleteAttributeValueController extends Controller
{
    public function post(Request $request)
    {
        $attributeId = $request->input('attributeId');
        $attributeValueId = $request->input('attributeValueId');

        try {
            $this->dispatch(new DeleteAttributeValueCommand($attributeValueId));
            $this->flashSuccess('Success removing attribute value');
        } catch (KommerceException $e) {
            $this->flashError('Unable remove attribute value.');
        }

        return redirect()->route(
            'admin.attribute.attribute-values',
            [
                'attributeId' => $attributeId,
            ]
        );
    }
}
