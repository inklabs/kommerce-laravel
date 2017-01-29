<?php
namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        return $this->renderTemplate(
            '@store/home/index.twig',
            [
                'newProducts' => $this->getRandomProducts(4),
                'recommendedProducts' => $this->getRandomProducts(12),
            ]
        );
    }
}
