<?php
namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use inklabs\kommerce\Action\Order\SetOrderStatusCommand;
use inklabs\kommerce\Exception\EntityValidatorException;

class SetOrderStatusController extends Controller
{
    public function index(Request $request)
    {
        $orderId = $request->input('orderId');
        $orderStatus = (int) $request->input('orderStatus');

        try {
            $this->dispatch(new SetOrderStatusCommand(
                $orderId,
                $orderStatus
            ));

            $this->flashSuccess('Changed Order Status.');
        } catch (EntityValidatorException $e) {
            $this->flashGenericWarning();
        }

        return redirect()->route('admin.order.view', ['orderId' => $orderId]);
    }
}
