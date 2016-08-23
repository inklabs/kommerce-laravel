<?php
namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        $this->displayTemplate(
            'home/index.twig',
            [
                'recommendedProducts' => $this->getRandomProducts(12),
            ]
        );
    }
}
