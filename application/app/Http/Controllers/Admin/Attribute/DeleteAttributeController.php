<?php
namespace App\Http\Controllers\Admin\Attribute;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Attribute\DeleteAttributeCommand;
use inklabs\kommerce\Exception\KommerceException;

class DeleteAttributeController extends Controller
{
    public function post(Request $request)
    {
        $attributeId = $request->input('attributeId');

        try {
            $this->dispatch(new DeleteAttributeCommand($attributeId));
            $this->flashSuccess('Success removing attribute');
        } catch (KommerceException $e) {
            $this->flashError('Unable remove attribute.');
        }

        return redirect()->route('admin.attribute');
    }
}
