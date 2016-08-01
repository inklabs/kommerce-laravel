<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use inklabs\kommerce\Exception\KommerceException;

class CheckoutController extends Controller
{
    public function getPay()
    {
        $cart = $this->getCart();

        $this->displayTemplate(
            'checkout/pay.twig',
            [
                'cart' => $cart,
                'months' => $this->getMonths(),
                'years' => $this->getYears(),
            ]
        );
    }

    public function postPay(Request $request)
    {
        dd($request->input());
    }

    private function getMonths()
    {
        $months = array('' => 'Month');

        for ($i = 1; $i <= 12; $i++) {
            $num = str_pad($i, 2, '0', STR_PAD_LEFT);
            $months[$num] = $num;
        }

        return $months;
    }

    private function getYears()
    {
        $years = array('' => 'Year');
        $current_year = (int) date('Y');

        for ($i = $current_year; $i <= ($current_year + 12); $i++) {
            $years[$i] = $i;
        }

        return $years;
    }
}
